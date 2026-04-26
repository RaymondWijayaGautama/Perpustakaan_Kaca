import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const LaporanPKLPanel = () => {
    const [dataLaporan, setDataLaporan] = useState([]);
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    
    const [searchJudul, setSearchJudul] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');
    const [filterTahun, setFilterTahun] = useState('');
    
    const [loading, setLoading] = useState(false);

    const fetchLaporan = useCallback(async () => {
        setLoading(true);
        try {
            // FIX 1: Alamat URL disamakan dengan routes/api.php (dihapus /buku-nya)
            const res = await axios.get('http://localhost:8000/api/laporan', {
                params: { 
                    page: laporanPage, 
                    judul: searchJudul,
                    penulis: filterPenulis,
                    tahun: filterTahun,
                    per_page: 5 // Sesuai pagination Laravel kamu
                }
            });
            setDataLaporan(res.data.data || []);
            setLaporanPagination(res.data);
        } catch (error) { 
            console.error("Gagal memuat laporan:", error); 
        } finally { 
            setLoading(false); 
        }
    }, [laporanPage, searchJudul, filterPenulis, filterTahun]);

    useEffect(() => {
        const handler = setTimeout(() => {
            fetchLaporan();
        }, 400);
        return () => clearTimeout(handler);
    }, [fetchLaporan]);

    // ==========================================
    // FUNGSI AKSI (MEMENUHI SYARAT RAYMOND)
    // ==========================================
    const handleHapus = async (id, judul) => {
        // Syarat Baris 18: Harus ada konfirmasi sebelum hapus!
        const yakin = window.confirm(`PERINGATAN: Yakin ingin menghapus laporan "${judul}"?\n\nJika laporan ini terhubung dengan fisik di rak, sistem akan menolak penghapusan.`);
        if (!yakin) return;

        try {
            await axios.delete(`http://localhost:8000/api/laporan/hapus/${id}`);
            alert("Mantap! Laporan berhasil dihapus.");
            fetchLaporan(); // Refresh data otomatis
        } catch (error) {
            alert(error.response?.data?.pesan || "Gagal menghapus data.");
        }
    };

    const handleEdit = () => {
        // Nanti bisa bos kembangin jadi Form Modal Popup
        alert("Fitur Edit akan membuka Form Modal/Popup untuk mengubah data & upload ulang PDF.");
    };

    const handleTambah = () => {
        // Nanti bisa bos kembangin jadi Form Modal Popup
        alert("Fitur Tambah akan membuka Form Upload Laporan PKL (PDF Max 10MB).");
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A] relative">
            {loading && (
                <div className="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl">
                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8 print:hidden">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat text-[#265F9C]">Laporan PKL</h1>
                    <p className="text-xs text-gray-500 mt-1">Arsip dan Referensi Laporan Siswa</p>
                </div>
                
                <div className="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    {/* TOMBOL TAMBAH DATA (Syarat Baris 16) */}
                    <button 
                        onClick={handleTambah}
                        className="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2 mr-2"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Laporan
                    </button>

                    <div className="relative flex-1 xl:w-64">
                        <input 
                            type="text" 
                            placeholder="Cari Judul..." 
                            className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" 
                            value={searchJudul} 
                            onChange={(e) => { setSearchJudul(e.target.value); setLaporanPage(1); }} 
                        />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div className="relative flex-1 xl:w-48">
                        <input 
                            type="text" 
                            placeholder="Filter Penulis..." 
                            className="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32]" 
                            value={filterPenulis} 
                            onChange={(e) => { setFilterPenulis(e.target.value); setLaporanPage(1); }} 
                        />
                        <div className="absolute left-3 top-3 text-[#2E7D32]">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div className="overflow-x-auto border border-gray-100 rounded-xl">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b border-gray-200">
                        <tr>
                            <th className="p-4 w-16 text-center">No</th>
                            <th className="p-4 w-2/5">Judul Laporan PKL</th>
                            <th className="p-4">Penulis (Siswa)</th>
                            <th className="p-4 text-center">Tahun</th>
                            <th className="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {dataLaporan.length > 0 ? dataLaporan.map((l, i) => (
                            <tr key={l.ISBN || i} className="text-sm hover:bg-blue-50/30 transition-colors">
                                <td className="p-4 text-center text-[#7D7D7E] font-mono">{(laporanPage - 1) * 5 + i + 1}</td>
                                <td className="p-4 font-bold text-[#265F9C] leading-snug">{l.judul_koleksi}</td>
                                <td className="p-4">
                                    <p className="font-semibold text-gray-800">{l.nama_siswa_tetap || 'Anonim'}</p>
                                </td>
                                <td className="p-4 text-center font-mono font-bold text-[#585858] bg-gray-50/50">{l.tahun}</td>
                                <td className="p-4 text-center">
                                    <button onClick={handleEdit} className="text-xs font-bold text-gray-500 hover:text-blue-600 mr-3">EDIT</button>
                                    <button onClick={() => handleHapus(l.ISBN, l.judul_koleksi)} className="text-xs font-bold text-red-500 hover:text-red-700">HAPUS</button>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="5" className="p-16 text-center">
                                    <p className="text-gray-400 italic font-montserrat text-sm">Laporan tidak ditemukan di database.</p>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 print:hidden">
                <button 
                    disabled={laporanPage === 1} 
                    onClick={() => setLaporanPage(laporanPage - 1)} 
                    className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm"
                > Prev </button>
                <span className="text-xs font-bold text-[#585858] uppercase">
                    Halaman <span className="text-[#265F9C]">{laporanPage}</span> / {laporanPagination.last_page || 1}
                </span>
                <button 
                    disabled={laporanPage >= (laporanPagination.last_page || 1)} 
                    onClick={() => setLaporanPage(laporanPage + 1)} 
                    className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm"
                > Next </button>
            </div>
        </div>
    );
};

export default LaporanPKLPanel;