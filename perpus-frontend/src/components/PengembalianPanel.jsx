import { useState, useEffect } from 'react';
import axios from 'axios';
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

const PengembalianPanel = ({ user }) => {
  const [idPinjam, setIdPinjam] = useState('');
  const [status, setStatus] = useState({ type: '', msg: '' });
  const [isScanning, setIsScanning] = useState(false);
  const [cameras, setCameras] = useState([]);
  const [selectedCameraId, setSelectedCameraId] = useState('');

  // 1. Inisialisasi daftar kamera perangkat
  useEffect(() => {
    Html5Qrcode.getCameras().then(devices => {
      if (devices && devices.length > 0) {
        setCameras(devices);
        setSelectedCameraId(devices[0].id);
      }
    }).catch(err => console.error("Gagal akses daftar kamera:", err));
  }, []);

  // 2. Konfigurasi Khusus Scanner untuk Code 128 (ISBN dengan tanda hubung)
  useEffect(() => {
    let html5QrCode = null;

    if (isScanning && selectedCameraId) {
      html5QrCode = new Html5Qrcode("reader");
      
      const config = { 
        fps: 25, 
        qrbox: { width: 450, height: 160 }, // Lebar ekstra untuk barcode Code 128
        aspectRatio: 1.777778, // Rasio 16:9 laptop
        // Memaksa sensor hanya fokus pada Code 128 & EAN 13 agar cepat terdeteksi
        formatsToSupport: [ 
          Html5QrcodeSupportedFormats.CODE_128,
          Html5QrcodeSupportedFormats.EAN_13 
        ]
      };

      html5QrCode.start(
        selectedCameraId, 
        config,
        (decodedText) => {
          setIdPinjam(decodedText);
          handleStopScanner(html5QrCode);
        },
        (errorMessage) => { /* Scanning... */ }
      ).catch(err => {
        console.error("Gagal start kamera:", err);
        setIsScanning(false);
      });
    }

    return () => {
      if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().catch(() => {});
      }
    };
  }, [isScanning, selectedCameraId]);

  const handleStopScanner = (scannerInstance) => {
    if (scannerInstance) {
      scannerInstance.stop().then(() => {
        setIsScanning(false);
      }).catch(err => console.error("Gagal stop scanner:", err));
    }
  };

  const handleReturn = async (e) => {
    e.preventDefault();
    setStatus({ type: '', msg: '' });
    try {
      const res = await axios.post('http://localhost:8000/api/kembalikan', {
        id_peminjaman: idPinjam,
        role: 'karyawan'
      });
      setStatus({ type: 'success', msg: res.data.message });
      setIdPinjam('');
    } catch (err) {
      setStatus({ type: 'error', msg: err.response?.data?.message || 'Gagal memproses pengembalian.' });
    }
  };

  return (
    /* Latar belakang default: #F6F7F9 */
    <div className="bg-white p-space-5 rounded-medium shadow-elevation-1 border border-gray-100 max-w-2xl mx-auto font-roboto">
      <header className="mb-space-4">
        {/* Heading: Montserrat SemiBold 28px */}
        <h2 className="text-brand-primary-500 font-montserrat text-[28px] font-semibold leading-[36px]">
          Pengembalian Buku
        </h2>
        {/* Body Small: 14px */}
        <p className="text-[#585858] text-[14px]">
          Arahkan barcode pada pratinjau kamera untuk mengisi nomor ISBN secara otomatis.
        </p>
      </header>

      {/* --- KONTROL KAMERA --- */}
      <div className="mb-space-4 flex flex-col gap-3">
        <div className="flex flex-col gap-1">
          <label className="text-[11px] font-bold text-neutral-900 uppercase tracking-widest">Pilih Sumber Kamera:</label>
          <select 
            value={selectedCameraId}
            onChange={(e) => setSelectedCameraId(e.target.value)}
            className="p-2 bg-[#F6F7F9] border border-gray-200 rounded-medium text-sm outline-none focus:ring-2 focus:ring-[#265F9C] transition-all"
            disabled={isScanning}
          >
            {cameras.map(camera => (
              <option key={camera.id} value={camera.id}>{camera.label}</option>
            ))}
          </select>
        </div>

        {/* --- BOX LIVE PREVIEW --- */}
        <div className={`relative overflow-hidden rounded-medium border-2 transition-all ${isScanning ? 'border-[#EDA60F] bg-black shadow-lg' : 'border-gray-200 bg-gray-50 h-24 flex items-center justify-center'}`}>
          <div id="reader" className="w-full"></div>
          
          {!isScanning && (
            <button 
              type="button"
              onClick={() => setIsScanning(true)}
              className="bg-[#265F9C] text-white px-6 py-2 rounded-medium font-bold text-[12px] shadow-md hover:brightness-110 active:scale-95 transition-all font-montserrat"
            >
              BUKA SCANNER BARCODE
            </button>
          )}

          {isScanning && (
             <div className="absolute top-3 left-3 bg-[#EDA60F] text-white px-3 py-1 rounded-medium text-[10px] font-bold uppercase animate-pulse shadow-sm">
                Kamera Aktif
             </div>
          )}
        </div>
        
        {isScanning && (
           <button 
            type="button"
            onClick={() => setIsScanning(false)}
            className="w-full py-2 text-[#C62828] font-bold text-[11px] uppercase border border-[#C62828] rounded-medium hover:bg-red-50 transition-colors tracking-widest"
           >
             Matikan Kamera
           </button>
        )}
      </div>

      <form onSubmit={handleReturn} className="space-y-space-3">
        <div className="flex flex-col gap-2">
          {/* Label Body Regular */}
          <label className="text-[#1A1A1A] font-semibold text-[14px]">Nomor ISBN / ID Pinjam</label>
          <input 
            type="text"
            value={idPinjam}
            onChange={(e) => setIdPinjam(e.target.value)}
            placeholder="ISBN akan terisi otomatis setelah scan..."
            className="p-space-2 border border-gray-300 rounded-medium focus:border-brand-primary-500 outline-none font-mono text-[16px] bg-gray-50 tracking-wider"
            required
          />
        </div>

        {/* Button Primary: #265F9C */}
        <button 
          type="submit" 
          className="w-full bg-[#265F9C] text-white py-space-2 rounded-medium font-bold font-montserrat uppercase text-[13px] hover:shadow-elevation-2 active:scale-95 transition-all tracking-[0.1em]"
        >
          Konfirmasi Pengembalian
        </button>
      </form>

      {/* --- PESAN STATUS --- */}
      {status.msg && (
        <div className={`mt-space-4 p-space-2 border-l-4 font-bold text-[13px] rounded-r-medium shadow-sm ${
          status.type === 'success' 
          ? 'bg-green-50 border-[#2E7D32] text-[#2E7D32]' 
          : 'bg-red-50 border-[#C62828] text-[#C62828]'
        }`}>
          {status.type === 'success' ? '✅ ' : '❌ '} {status.msg}
        </div>
      )}

      <footer className="mt-space-5 pt-space-3 border-t border-gray-100 flex justify-between items-center opacity-60">
        <span className="text-[10px] uppercase font-bold tracking-tighter">Petugas: {user?.nama || 'Pustakawan'}</span>
        <span className="text-[10px] font-mono italic">KACA System v1.0</span>
      </footer>
    </div>
  );
};

export default PengembalianPanel;