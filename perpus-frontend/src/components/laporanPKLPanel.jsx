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

    // ==========================================
    // STATE UNTUK MODAL FORM (TAMBAH & EDIT)
    // ==========================================
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState('add'); // 'add' atau 'edit'
    const [formData, setFormData] = useState({
        isbn: '', judul_koleksi: '', pengarang: '', tahun: '', file_laporan: null
    });

    const fetchLaporan = useCallback(async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/laporan', {
                params: { 
                    page: laporanPage, judul: searchJudul, penulis: filterPenulis, tahun: filterTahun, per_page: 5 
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
        const handler = setTimeout(() => { fetchLaporan(); }, 400);
        return () => clearTimeout(handler);
    }, [fetchLaporan]);

    // ==========================================
    // FUNGSI AKSI (MEMENUHI SYARAT RAYMOND)
    // ==========================================
    const handleHapus = async (id, judul) => {
        const yakin = window.confirm(`PERINGATAN: Yakin ingin menghapus laporan "${judul}"?\n\nJika laporan ini sedang digunakan/dipinjam, sistem akan menolak penghapusan.`);
        if (!yakin) return;

        try {
            await axios.delete(`http://localhost:8000/api/laporan/hapus/${id}`);
            alert("Mantap! Laporan berhasil dihapus.");
            fetchLaporan();
        } catch (error) {
            alert(error.response?.data?.pesan || "Gagal menghapus data.");
        }
    };

    // Buka Modal untuk Tambah
    const openAddModal = () => {
        setModalMode('add');
        setFormData({ isbn: '', judul_koleksi: '', pengarang: '', tahun: '', file_laporan: null });
        setIsModalOpen(true);
    };

    // Buka Modal untuk Edit
    const openEditModal = (laporan) => {
        setModalMode('edit');
        setFormData({ 
            isbn: laporan.ISBN, 
            judul_koleksi: laporan.judul_koleksi, 
            pengarang: laporan.nama_siswa_tetap, 
            tahun: laporan.tahun, 
            file_laporan: null 
        });
        setIsModalOpen(true);
    };

    // Proses Submit Data & File
    const handleSubmitForm = async (e) => {
        e.preventDefault();
        
        // Cek ukuran file max 10MB (Syarat Baris 16)
        if (formData.file_laporan && formData.file_laporan.size > 10 * 1024 * 1024) {
            alert("Ukuran file terlalu besar! Maksimal 10MB.");
            return;
        }

        // Pakai FormData karena kita kirim file
        const data = new FormData();
        data.append('judul_koleksi', formData.judul_koleksi);
        data.append('pengarang', formData.pengarang);
        data.append('tahun', formData.tahun);
        if (formData.file_laporan) {
            data.append('file_laporan', formData.file_laporan);
        }

        try {
            if (modalMode === 'add') {
                await axios.post('http://localhost:8000/api/laporan/tambah', data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                alert("Berhasil menambahkan laporan baru!");
            } else {
                // Laravel butuh _method=PUT kalau kirim FormData untuk update
                data.append('_method', 'PUT'); 
                await axios.post(`http://localhost:8000/api/laporan/ubah/${formData.isbn}`, data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                alert("Berhasil mengubah laporan!");
            }
            setIsModalOpen(false);
            fetchLaporan();
        } catch (error) {
            alert("Gagal menyimpan: " + (error.response?.data?.pesan || error.message));
        }
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A] relative">
            {loading && (
                <div className="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl">
                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            {/* HEADER */}
            <div className="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8 print:hidden">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat text-[#265F9C]">Laporan PKL</h1>
                    <p className="text-xs text-gray-500 mt-1">Arsip dan Referensi Laporan Siswa</p>
                </div>
                
                <div className="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    <button 
                        onClick={openAddModal}
                        className="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2 mr-2"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Laporan
                    </button>

                    <div className="relative flex-1 xl:w-64">
                        <input type="text" placeholder="Cari Judul..." className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" value={searchJudul} onChange={(e) => { setSearchJudul(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div className="relative flex-1 xl:w-48">
                        <input type="text" placeholder="Filter Penulis..." className="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32]" value={filterPenulis} onChange={(e) => { setFilterPenulis(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-3 text-[#2E7D32]">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {/* TABEL */}
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
                                <td className="p-4"><p className="font-semibold text-gray-800">{l.nama_siswa_tetap || 'Anonim'}</p></td>
                                <td className="p-4 text-center font-mono font-bold text-[#585858] bg-gray-50/50">{l.tahun}</td>
                                <td className="p-4 text-center">
                                    <button onClick={() => openEditModal(l)} className="text-xs font-bold text-gray-500 hover:text-blue-600 mr-3">EDIT</button>
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

            {/* PAGINATION */}
            <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 print:hidden">
                <button disabled={laporanPage === 1} onClick={() => setLaporanPage(laporanPage - 1)} className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm"> Prev </button>
                <span className="text-xs font-bold text-[#585858] uppercase">
                    Halaman <span className="text-[#265F9C]">{laporanPage}</span> / {laporanPagination.last_page || 1}
                </span>
                <button disabled={laporanPage >= (laporanPagination.last_page || 1)} onClick={() => setLaporanPage(laporanPage + 1)} className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm"> Next </button>
            </div>

            {/* MODAL FORM TAMBAH/EDIT */}
            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                        <div className="bg-[#265F9C] p-4 flex justify-between items-center text-white">
                            <h3 className="font-bold font-montserrat">{modalMode === 'add' ? 'Tambah Laporan PKL' : 'Ubah Laporan PKL'}</h3>
                            <button onClick={() => setIsModalOpen(false)} className="hover:text-red-300 font-bold">✕</button>
                        </div>
                        <form onSubmit={handleSubmitForm} className="p-6 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-gray-600 mb-1">Judul Laporan</label>
                                {/* MAX LENGTH 25 SESUAI DATABASE SEKOLAH */}
                                <input type="text" required maxLength="25" placeholder="Maksimal 25 huruf..." className="w-full p-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={formData.judul_koleksi} onChange={(e) => setFormData({...formData, judul_koleksi: e.target.value})} />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-gray-600 mb-1">Penulis (Nama Siswa)</label>
                                    {/* MAX LENGTH 25 SESUAI DATABASE SEKOLAH */}
                                    <input type="text" required maxLength="25" className="w-full p-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={formData.pengarang} onChange={(e) => setFormData({...formData, pengarang: e.target.value})} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-600 mb-1">Tahun Laporan</label>
                                    <input type="number" required min="2000" max="2099" className="w-full p-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={formData.tahun} onChange={(e) => setFormData({...formData, tahun: e.target.value})} />
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-600 mb-1">Upload File (PDF/Doc, Max 10MB)</label>
                                <input type="file" accept=".pdf,.doc,.docx" required={modalMode === 'add'} className="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#265F9C] hover:file:bg-blue-100 cursor-pointer" onChange={(e) => setFormData({...formData, file_laporan: e.target.files[0]})} />
                                {modalMode === 'edit' && <p className="text-[10px] text-gray-400 mt-1">*Abaikan jika tidak ingin mengganti file laporan.</p>}
                            </div>
                            <div className="flex justify-end gap-3 mt-8 pt-4 border-t">
                                <button type="button" onClick={() => setIsModalOpen(false)} className="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl text-sm transition-colors">Batal</button>
                                <button type="submit" className="px-5 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white font-bold rounded-xl text-sm transition-colors shadow-md">Simpan Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default LaporanPKLPanel;