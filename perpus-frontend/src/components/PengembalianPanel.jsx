import { useState, useRef, useEffect } from 'react';
import axios from 'axios';

const PengembalianBulkPanel = () => {
  const [memberInput, setMemberInput] = useState('');
  const [memberData, setMemberData] = useState(null);
  const [tglKembaliManual, setTglKembaliManual] = useState(new Date().toISOString().split('T')[0]);
  const [bukuInput, setBukuInput] = useState('');
  const [daftarKembali, setDaftarKembali] = useState([]);
  const [loading, setLoading] = useState(false);
  
  // State untuk Pop-up Toast
  const [toast, setToast] = useState({ show: false, type: '', text: '' });

  const inputBukuRef = useRef(null);

  // Fungsi pembantu untuk memunculkan Pop-up
  const showToast = (type, text) => {
    setToast({ show: true, type, text });
    // Hilangkan pop-up otomatis setelah 4 detik
    setTimeout(() => {
      setToast({ show: false, type: '', text: '' });
    }, 4000);
  };

  // 1. Identifikasi Pemustaka
  const cariMember = async () => {
    try {
      const res = await axios.get(`http://localhost:8000/api/anggota/${memberInput}`);
      setMemberData(res.data);
    } catch (err) {
      showToast('error', 'IDENTITAS TIDAK DITEMUKAN');
    }
  };

  // 2. Tambah Buku ke Daftar
  const tambahBuku = async (e) => {
    e.preventDefault();
    if (!memberData) return;

    try {
      const res = await axios.get(`http://localhost:8000/api/peminjaman/cek-aktif`, {
        params: { id_pinjam: bukuInput, id_member: memberData.id_siswa_tetap || memberData.id_karyawan }
      });

      const dataPinjam = res.data;

      if (daftarKembali.some(item => item.id_peminjaman === dataPinjam.id_peminjaman)) {
        showToast('error', 'BUKU SUDAH ADA DI DALAM DAFTAR PENGEMBALIAN');
        setBukuInput('');
        return;
      }

      const deadline = new Date(dataPinjam.tgl_harus_kembali);
      const realita = new Date(tglKembaliManual);
      const terlambat = realita > deadline ? Math.ceil((realita - deadline) / (1000 * 60 * 60 * 24)) : 0;

      setDaftarKembali([...daftarKembali, {
        ...dataPinjam,
        kondisi: 'Baik',
        tgl_kembali_manual: tglKembaliManual,
        estimasi_terlambat: terlambat
      }]);

      setBukuInput('');
      inputBukuRef.current.focus(); 
    } catch (err) {
      showToast('error', 'BUKU TIDAK TERDAFTAR PADA PEMINJAM INI');
    }
  };

  // 3. Eksekusi ke Backend
  const prosesPengembalian = async () => {
    setLoading(true);
    try {
      await axios.post('http://localhost:8000/api/pengembalian/batch', {
        items: daftarKembali
      });
      
      // Memanggil Pop-up Sukses
      showToast('success', `${daftarKembali.length} KOLEKSI BERHASIL DIKEMBALIKAN (A.N. ${memberData.nama_siswa_tetap || memberData.nama_karyawan})`);
      
      // Reset form
      setDaftarKembali([]);
      setMemberData(null);
      setMemberInput('');
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'GAGAL MEMPROSES DATA';
      showToast('error', `ERROR: ${errorMessage}`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="relative p-10 bg-white border border-slate-200 rounded shadow-sm max-w-5xl mx-auto font-mono text-xs overflow-hidden">
      
      {/* ========================================= */}
      {/* POP-UP TOAST MELAYANG DI POJOK KANAN ATAS */}
      {/* ========================================= */}
      {toast.show && (
        <div className="fixed top-6 right-6 z-50 animate-bounce">
          <div className={`flex items-center justify-between min-w-[300px] p-4 rounded shadow-2xl border-l-4 ${toast.type === 'success' ? 'bg-slate-900 border-green-500 text-white' : 'bg-red-50 border-red-600 text-red-800'}`}>
            <div className="flex items-center gap-3">
              <span className="text-xl">{toast.type === 'success' ? '✅' : '⚠️'}</span>
              <span className="font-bold uppercase tracking-widest leading-relaxed">
                {toast.text}
              </span>
            </div>
            <button onClick={() => setToast({ show: false, type: '', text: '' })} className="ml-6 text-slate-400 hover:text-white font-bold">
              ✕
            </button>
          </div>
        </div>
      )}
      {/* ========================================= */}

      <h2 className="text-slate-900 font-bold uppercase tracking-tighter mb-10 border-b pb-4 text-lg">
        Form Entry Pengembalian Koleksi
      </h2>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
        {/* Kontrol Identitas */}
        <div className="space-y-4">
          <label className="font-bold text-slate-400 uppercase tracking-widest block">01. Identitas Pemustaka</label>
          <div className="flex gap-2">
            <input 
              type="text" 
              value={memberInput} 
              onChange={(e) => setMemberInput(e.target.value)} 
              className="flex-1 p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50"
              placeholder="NIP / NISN"
            />
            <button onClick={cariMember} className="bg-slate-900 text-white px-6 py-2 font-bold uppercase hover:bg-black transition-colors">Cari</button>
          </div>
          {memberData && (
            <div className="p-4 bg-slate-100 border-l-4 border-slate-900">
              <p className="font-bold uppercase">{memberData.nama_siswa_tetap || memberData.nama_karyawan}</p>
              <p className="text-slate-500 uppercase">Status Terverifikasi</p>
            </div>
          )}
        </div>

        {/* Kontrol Tanggal Manual */}
        <div className="space-y-4">
          <label className="font-bold text-slate-400 uppercase tracking-widest block">02. Parameter Tanggal</label>
          <input 
            type="date" 
            value={tglKembaliManual} 
            onChange={(e) => setTglKembaliManual(e.target.value)}
            className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold"
          />
          <p className="text-[10px] text-slate-400 leading-relaxed italic">
            * Sesuaikan tanggal jika pengembalian fisik terjadi di masa lalu.
          </p>
        </div>
      </div>

      {/* Input Buku */}
      {memberData && (
        <div className="mb-10 p-6 bg-slate-50 border border-slate-200">
          <label className="font-bold text-slate-400 uppercase tracking-widest block mb-4">03. Daftar Buku Kembali</label>
          <form onSubmit={tambahBuku} className="flex gap-2">
            <input 
              ref={inputBukuRef}
              type="text" 
              value={bukuInput} 
              onChange={(e) => setBukuInput(e.target.value)}
              className="flex-1 p-3 border border-slate-300 outline-none focus:border-slate-900 font-bold"
              placeholder="Scan ISBN atau Input Manual ID Buku"
            />
            <button type="submit" className="bg-slate-200 text-slate-900 border border-slate-300 px-8 py-2 font-bold uppercase hover:bg-slate-300 transition-colors">Input</button>
          </form>
        </div>
      )}

      {/* Tabel Review */}
      {daftarKembali.length > 0 && (
        <div className="mb-10 overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-slate-200 text-slate-400">
                <th className="py-4 uppercase tracking-widest">Judul Koleksi</th>
                <th className="py-4 uppercase tracking-widest">Deadline</th>
                <th className="py-4 uppercase tracking-widest">Status Kalkulasi</th>
                <th className="py-4 uppercase tracking-widest">Kondisi</th>
                <th className="py-4 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {daftarKembali.map((item, index) => (
                <tr key={index} className="border-b border-slate-100">
                  <td className="py-4 font-bold uppercase">{item.judul_koleksi}</td>
                  <td className="py-4 text-slate-500">{item.tgl_harus_kembali}</td>
                  <td className="py-4">
                    {item.estimasi_terlambat > 0 ? (
                      <span className="text-red-700 font-bold bg-red-50 px-2 py-1 border border-red-200">TERLAMBAT {item.estimasi_terlambat} HARI (SP 1)</span>
                    ) : (
                      <span className="text-slate-900 font-bold">TEPAT WAKTU</span>
                    )}
                  </td>
                  <td className="py-4">
                    <select 
                      className="p-1 border border-slate-200 outline-none bg-white font-bold uppercase"
                      value={item.kondisi}
                      onChange={(e) => {
                        const newDaftar = [...daftarKembali];
                        newDaftar[index].kondisi = e.target.value;
                        setDaftarKembali(newDaftar);
                      }}
                    >
                      <option value="Baik">BAIK</option>
                      <option value="Rusak">RUSAK</option>
                      <option value="Hilang">HILANG</option>
                    </select>
                  </td>
                  <td className="py-4 text-right">
                    <button onClick={() => setDaftarKembali(daftarKembali.filter((_, i) => i !== index))} className="text-red-600 font-bold hover:text-red-800 transition-colors uppercase">Cancel</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          <button 
            onClick={prosesPengembalian}
            disabled={loading}
            className="w-full mt-10 bg-slate-900 text-white py-4 font-bold uppercase tracking-[0.2em] hover:bg-black transition-all disabled:bg-slate-400 cursor-pointer disabled:cursor-not-allowed"
          >
            {loading ? 'Processing Data...' : 'Submit Seluruh Pengembalian'}
          </button>
        </div>
      )}
    </div>
  );
};

export default PengembalianBulkPanel;