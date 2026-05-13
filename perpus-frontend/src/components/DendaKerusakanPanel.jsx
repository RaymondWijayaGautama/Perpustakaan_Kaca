import { useState } from 'react';
import axios from 'axios';

const API_BASE = 'http://localhost:8000/api';

const DendaKerusakanPanel = () => {
  const [searchInput, setSearchInput] = useState('');
  const [hasilCari, setHasilCari] = useState([]);
  const [loadingCari, setLoadingCari] = useState(false);

  // State form input denda per baris
  const [formDenda, setFormDenda] = useState({}); // { id_peminjaman: { deskripsi, nominal } }

  const [savingId, setSavingId] = useState(null);
  const [toast, setToast] = useState({ show: false, type: '', text: '' });

  const showToast = (type, text) => {
    setToast({ show: true, type, text });
    setTimeout(() => setToast({ show: false, type: '', text: '' }), 4000);
  };

  const handleCari = async (e) => {
    e.preventDefault();
    if (!searchInput.trim()) return;
    setLoadingCari(true);
    setHasilCari([]);
    try {
      const res = await axios.get(`${API_BASE}/denda-kerusakan/cari`, {
        params: { search: searchInput.trim() },
      });
      const data = res.data?.data || [];
      setHasilCari(data);

      // Inisialisasi form denda dari data yang sudah ada di DB
      const init = {};
      data.forEach(item => {
        init[item.id_peminjaman] = {
          deskripsi: item.kondisi_buku || '',
          nominal: item.denda_peminjaman || '',
        };
      });
      setFormDenda(init);

      if (data.length === 0) showToast('error', 'TIDAK ADA DATA DITEMUKAN');
    } catch (err) {
      showToast('error', err.response?.data?.message || 'GAGAL MENCARI DATA');
    } finally {
      setLoadingCari(false);
    }
  };

  const handleFormChange = (idPeminjaman, field, value) => {
    setFormDenda(prev => ({
      ...prev,
      [idPeminjaman]: {
        ...prev[idPeminjaman],
        [field]: value,
      },
    }));
  };

  const handleSimpan = async (idPeminjaman) => {
    const form = formDenda[idPeminjaman] || {};
    if (!form.deskripsi || !form.nominal) {
      showToast('error', 'DESKRIPSI DAN NOMINAL DENDA WAJIB DIISI');
      return;
    }
    if (Number(form.nominal) <= 0) {
      showToast('error', 'NOMINAL DENDA HARUS LEBIH DARI 0');
      return;
    }

    setSavingId(idPeminjaman);
    try {
      await axios.post(`${API_BASE}/denda-kerusakan/simpan`, {
        id_peminjaman: idPeminjaman,
        deskripsi_kerusakan: form.deskripsi,
        nominal_denda: Number(form.nominal),
      });
      showToast('success', `DENDA TRANSAKSI #${idPeminjaman} BERHASIL DICATAT`);

      // Update tampilan lokal
      setHasilCari(prev => prev.map(item =>
        item.id_peminjaman === idPeminjaman
          ? { ...item, kondisi_buku: form.deskripsi, denda_peminjaman: form.nominal }
          : item
      ));
    } catch (err) {
      showToast('error', err.response?.data?.message || 'GAGAL MENYIMPAN DATA');
    } finally {
      setSavingId(null);
    }
  };

  return (
    <div className="relative p-10 bg-white border border-slate-200 rounded shadow-sm max-w-6xl mx-auto font-mono text-xs overflow-hidden">

      {/* TOAST */}
      {toast.show && (
        <div className="fixed top-6 right-6 z-50 animate-bounce">
          <div className={`flex items-center justify-between min-w-[320px] p-4 rounded shadow-2xl border-l-4 ${toast.type === 'success' ? 'bg-slate-900 border-green-500 text-white' : 'bg-red-50 border-red-600 text-red-800'}`}>
            <div className="flex items-center gap-3">
              <span className="text-xl">{toast.type === 'success' ? '✅' : '⚠️'}</span>
              <span className="font-bold uppercase tracking-widest leading-relaxed">{toast.text}</span>
            </div>
            <button onClick={() => setToast({ show: false, type: '', text: '' })} className="ml-6 text-slate-400 hover:text-white font-bold">✕</button>
          </div>
        </div>
      )}

      {/* HEADER */}
      <div className="border-b pb-4 mb-8">
        <h2 className="text-slate-900 font-bold uppercase tracking-tighter text-lg">
          Kalkulasi Denda Kerusakan Buku
        </h2>
        <p className="text-slate-500 mt-1 leading-relaxed">
          Pustakawan mencari transaksi peminjaman, lalu menginput deskripsi kerusakan dan nominal denda.
        </p>
      </div>

      {/* FORM PENCARIAN */}
      <div className="mb-8 p-6 bg-slate-50 border border-slate-200">
        <label className="font-bold text-slate-400 uppercase tracking-widest block mb-3">
          Cari Transaksi Peminjaman
        </label>
        <form onSubmit={handleCari} className="flex gap-3">
          <input
            type="text"
            value={searchInput}
            onChange={e => setSearchInput(e.target.value)}
            className="flex-1 p-3 border border-slate-300 outline-none focus:border-slate-900 bg-white font-bold"
            placeholder="Ketik No. Transaksi, NISN, NIP, atau Nama Pemustaka..."
          />
          <button
            type="submit"
            disabled={loadingCari}
            className="bg-slate-900 text-white px-10 py-3 font-bold uppercase tracking-widest hover:bg-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {loadingCari ? '...' : 'Cari'}
          </button>
        </form>
        <p className="mt-2 text-[10px] text-slate-400 italic">
          * Menampilkan maks. 20 transaksi terbaru. Gunakan No. Transaksi untuk pencarian lebih tepat.
        </p>
      </div>

      {/* TABEL HASIL */}
      {hasilCari.length > 0 && (
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse border border-slate-200">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-300">
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">No. Transaksi</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Pemustaka</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Judul Buku</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Status</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200 min-w-[200px]">Deskripsi Kerusakan</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200 min-w-[140px]">Nominal Denda (Rp)</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {hasilCari.map((item) => {
                const form = formDenda[item.id_peminjaman] || { deskripsi: '', nominal: '' };
                const isSaving = savingId === item.id_peminjaman;
                const sudahAda = item.denda_peminjaman && item.denda_peminjaman > 0;

                return (
                  <tr key={item.id_peminjaman} className="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <td className="py-3 px-4 font-bold border-r border-slate-100">
                      <span className="bg-slate-100 px-2 py-1">#{item.id_peminjaman}</span>
                    </td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <span className="font-bold uppercase">{item.nama_peminjam || '-'}</span>
                      <br />
                      <span className="text-[10px] text-slate-400">{item.identitas_peminjam || '-'}</span>
                    </td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <span className="font-bold">{item.judul_koleksi || '-'}</span>
                      <br />
                      <span className="text-[10px] text-slate-400">{item.tgl_pinjam ? `Pinjam: ${item.tgl_pinjam}` : ''}</span>
                    </td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <span className={`px-2 py-1 text-[10px] font-bold uppercase ${
                        item.status_peminjaman === 'Dipinjam' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                        item.status_peminjaman === 'Terlambat' ? 'bg-red-50 text-red-700 border border-red-200' :
                        'bg-green-50 text-green-700 border border-green-200'
                      }`}>
                        {item.status_peminjaman || '-'}
                      </span>
                    </td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <input
                        type="text"
                        className="w-full p-2 border border-slate-200 outline-none focus:border-slate-900 bg-white font-bold"
                        value={form.deskripsi}
                        placeholder="Contoh: Cover sobek, halaman basah..."
                        onChange={e => handleFormChange(item.id_peminjaman, 'deskripsi', e.target.value)}
                      />
                    </td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <div className="flex items-center border border-slate-200 focus-within:border-slate-900 bg-white">
                        <span className="bg-slate-100 px-2 py-2 text-slate-500 border-r border-slate-200 font-bold">Rp</span>
                        <input
                          type="number"
                          min="0"
                          className="flex-1 p-2 outline-none font-bold text-right"
                          value={form.nominal}
                          placeholder="0"
                          onChange={e => handleFormChange(item.id_peminjaman, 'nominal', e.target.value)}
                        />
                      </div>
                    </td>
                    <td className="py-3 px-4 text-center">
                      <button
                        onClick={() => handleSimpan(item.id_peminjaman)}
                        disabled={isSaving}
                        className="bg-slate-900 text-white px-4 py-2 font-bold uppercase hover:bg-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {isSaving ? '...' : sudahAda ? 'Update' : 'Simpan'}
                      </button>
                      {sudahAda && (
                        <div className="mt-1 text-[10px] text-green-700 font-bold">✓ Tercatat</div>
                      )}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      )}

      {!loadingCari && hasilCari.length === 0 && searchInput && (
        <div className="text-center py-12 text-slate-400 font-bold uppercase tracking-widest">
          Tidak ada data. Coba cari dengan kata kunci lain.
        </div>
      )}
    </div>
  );
};

export default DendaKerusakanPanel;
