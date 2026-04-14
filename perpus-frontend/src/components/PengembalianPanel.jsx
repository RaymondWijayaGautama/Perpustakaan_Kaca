import { useState, useEffect } from 'react';
import axios from 'axios';
import { Html5Qrcode } from 'html5-qrcode';

const PengembalianPanel = () => {
  const [idPinjam, setIdPinjam] = useState('');
  const [status, setStatus] = useState({ type: '', msg: '' });
  const [isScanning, setIsScanning] = useState(false);
  const [cameras, setCameras] = useState([]);
  const [selectedCameraId, setSelectedCameraId] = useState('');

  const stopScanner = (scannerInstance) => {
    if (scannerInstance) {
      scannerInstance.stop().then(() => {
        setIsScanning(false);
      }).catch(err => console.error("Gagal stop scanner:", err));
    }
  };

  // 1. Ambil daftar kamera saat pertama kali dimuat
  useEffect(() => {
    Html5Qrcode.getCameras().then(devices => {
      if (devices && devices.length > 0) {
        setCameras(devices);
        setSelectedCameraId(devices[0].id);
      }
    }).catch(err => console.error("Gagal akses daftar kamera:", err));
  }, []);

  // 2. Logika untuk Menghidupkan/Mematikan Kamera Preview
  useEffect(() => {
    let html5QrCode = null;

    if (isScanning && selectedCameraId) {
      html5QrCode = new Html5Qrcode("reader");
      
      const config = { 
        fps: 20, 
        qrbox: { width: 300, height: 150 },
        aspectRatio: 1.777778 
      };

      // Memulai kamera secara otomatis
      html5QrCode.start(
        selectedCameraId, 
        config,
        (decodedText) => {
          setIdPinjam(decodedText);
          stopScanner(html5QrCode);
        },
        () => { /* Scanning... */ }
      ).catch(err => {
        console.error("Gagal start kamera:", err);
        setIsScanning(false);
      });
    }

    return () => {
      if (html5QrCode && html5QrCode.isScanning) {
        stopScanner(html5QrCode);
      }
    };
  }, [isScanning, selectedCameraId]);

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
      setStatus({ type: 'error', msg: err.response?.data?.message || 'Gagal memproses' });
    }
  };

  return (
    <div className="bg-white p-space-5 rounded-medium shadow-elevation-1 border border-gray-100 max-w-2xl mx-auto">
      <header className="mb-space-4">
        <h2 className="text-brand-primary-500 font-montserrat text-[28px] font-semibold leading-[36px]">
          Pengembalian Buku
        </h2>
        <p className="text-[#585858] font-roboto text-[14px]">
          Gunakan pratinjau kamera di bawah untuk mengarahkan barcode ISBN buku.
        </p>
      </header>

      {/* --- KONTROL KAMERA --- */}
      <div className="mb-space-4 grid grid-cols-1 gap-3">
        <div className="flex flex-col gap-1">
          <label className="text-[11px] font-bold text-neutral-900 uppercase">Pilih Perangkat Kamera:</label>
          <select 
            value={selectedCameraId}
            onChange={(e) => setSelectedCameraId(e.target.value)}
            className="p-2 bg-[#F6F7F9] border border-gray-200 rounded-medium text-sm outline-none focus:ring-2 focus:ring-brand-primary-500 transition-all"
            disabled={isScanning}
          >
            {cameras.map(camera => (
              <option key={camera.id} value={camera.id}>{camera.label}</option>
            ))}
          </select>
        </div>

        {/* --- BOX LIVE PREVIEW --- */}
        <div className={`relative overflow-hidden rounded-medium border-2 transition-all ${isScanning ? 'border-brand-secondary-500 bg-black' : 'border-gray-200 bg-gray-100 h-20 flex items-center justify-center'}`}>
          <div id="reader" className="w-full"></div>
          
          {!isScanning && (
            <button 
              type="button"
              onClick={() => setIsScanning(true)}
              className="bg-brand-primary-500 text-white px-6 py-2 rounded-medium font-bold text-[12px] shadow-md hover:scale-105 transition-transform"
            >
              AKTIFKAN PRATINJAU KAMERA
            </button>
          )}

          {isScanning && (
             <div className="absolute top-2 left-2 bg-brand-secondary-500 text-white px-2 py-1 rounded text-[10px] font-bold uppercase animate-pulse">
                Live Preview Aktif
             </div>
          )}
        </div>
        
        {isScanning && (
           <button 
            type="button"
            onClick={() => setIsScanning(false)}
            className="w-full py-2 text-[#C62828] font-bold text-[10px] uppercase border border-[#C62828] rounded-medium hover:bg-red-50 transition-colors"
           >
             Matikan Kamera
           </button>
        )}
      </div>

      <form onSubmit={handleReturn} className="space-y-space-3">
        <div className="flex flex-col gap-2">
          <label className="text-neutral-900 font-medium text-[14px]">Nomor ISBN / ID Pinjam</label>
          <input 
            type="text"
            value={idPinjam}
            onChange={(e) => setIdPinjam(e.target.value)}
            placeholder="Hasil scan akan masuk ke sini..."
            className="p-space-2 border border-gray-300 rounded-medium focus:border-brand-primary-500 outline-none font-mono text-[15px] bg-gray-50"
            required
          />
        </div>

        <button 
          type="submit" 
          className="w-full bg-brand-primary-500 text-white py-space-2 rounded-medium font-bold font-montserrat uppercase text-[13px] hover:shadow-elevation-2 active:scale-95 transition-all"
        >
          Konfirmasi Pengembalian
        </button>
      </form>

      {/* Pesan Status */}
      {status.msg && (
        <div className={`mt-space-4 p-space-2 border-l-4 font-bold text-[13px] ${status.type === 'success' ? 'bg-green-50 border-success text-success' : 'bg-red-50 border-error text-error'}`}>
          {status.msg}
        </div>
      )}
    </div>
  );
};

export default PengembalianPanel;
