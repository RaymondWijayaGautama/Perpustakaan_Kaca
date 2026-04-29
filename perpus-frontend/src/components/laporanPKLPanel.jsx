import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const LaporanPKLPanel = () => {
    const [dataLaporan, setDataLaporan] = useState([]);
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    
    const [searchJudul, setSearchJudul] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');
    const [loading, setLoading] = useState(false);

    // STATE MODAL FORM
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState('add');
    const [formData, setFormData] = useState({
        isbn: '', judul_koleksi: '', pengarang: '', tahun: '', file_laporan: null
    });

    // FETCH DATA
    const fetchLaporan = useCallback(async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/laporan', {
                params: { page: laporanPage, judul: searchJudul, penulis: filterPenulis, per_page: 5 }
            });
            setDataLaporan(res.data.data || []);
            setLaporanPagination(res.data);
        } catch (error) { 
            console.error("Gagal memuat laporan:", error); 
        } finally { 
            setLoading(false); 
        }
    }, [laporanPage, searchJudul, filterPenulis]);

    useEffect(() => {
        const handler = setTimeout(() => { fetchLaporan(); }, 400);
        return () => clearTimeout(handler);
    }, [fetchLaporan]);

    // HAPUS DATA
    const handleHapus = async (id, judul) => {
        const yakin = window.confirm(`Yakin ingin menghapus laporan "${judul}"?`);
        if (!yakin) return;
        try {
            await axios.delete(`http://localhost:8000/api/laporan/hapus/${id}`);
            alert("Laporan berhasil dihapus.");
            fetchLaporan();
        } catch (error) {
            alert(error.response?.data?.pesan || "Gagal menghapus data.");
        }
    };

    // BUKA MODAL TAMBAH
    const openAddModal = () => {
        setModalMode('add');
        setFormData({ isbn: '', judul_koleksi: '', pengarang: '', tahun: '', file_laporan: null });
        setIsModalOpen(true);
    };

    // BUKA MODAL UBAH
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

    // SUBMIT FORM
    const handleSubmitForm = async (e) => {
        e.preventDefault();
        if (formData.file_laporan && formData.file_laporan.size > 10 * 1024 * 1024) {
            alert("Ukuran file terlalu besar! Maksimal 10MB.");
            return;
        }

        const data = new FormData();
        data.append('judul_koleksi', formData.judul_koleksi);
        data.append('pengarang', formData.pengarang);
        data.append('tahun', formData.tahun);
        if (formData.file_laporan) data.append('file_laporan', formData.file_laporan);

        try {
            if (modalMode === 'add') {
                await axios.post('http://localhost:8000/api/laporan/tambah', data, { headers: { 'Content-Type': 'multipart/form-data' } });
                alert("Laporan berhasil ditambahkan!");
            } else {
                data.append('_method', 'PUT'); 
                await axios.post(`http://localhost:8000/api/laporan/ubah/${formData.isbn}`, data, { headers: { 'Content-Type': 'multipart/form-data' } });
                alert("Laporan berhasil diubah!");
            }
            setIsModalOpen(false);
            fetchLaporan();
        } catch (error) {
            alert("Gagal menyimpan: " + (error.response?.data?.pesan || error.message));
        }
    };

    return (
        <div className="bg-white rounded-xl shadow-sm p-8 border border-gray-100 text-[#1A1A1A] relative min-h-[500px]">
            {loading && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl">
                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            {/* HEADER AREA */}
            <div className="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8 print:hidden">
                <div>
                    <h1 className="text-3xl font-bold font-montserrat text-[#265F9C] tracking-tight">Laporan PKL</h1>
                    <p className="text-sm text-gray-500 mt-1">Arsip dan Referensi Laporan Siswa</p>
                </div>
                
                <div className="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                    {/* TOMBOL TAMBAH */}
                    <button onClick={openAddModal} className="px-6 py-2.5 bg-[#00A651] hover:bg-[#008f45] text-white rounded-lg text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Laporan
                    </button>

                    {/* SEARCH INPUT */}
                    <div className="relative w-full xl:w-64">
                        <input type="text" placeholder="Cari Judul..." className="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#265F9C] focus:border-transparent transition-all" value={searchJudul} onChange={(e) => { setSearchJudul(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    {/* FILTER INPUT */}
                    <div className="relative w-full xl:w-56">
                        <input type="text" placeholder="Filter Penulis..." className="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#00A651] focus:border-transparent transition-all" value={filterPenulis} onChange={(e) => { setFilterPenulis(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-2.5 text-[#00A651]">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {/* TABLE AREA */}
            <div className="w-full mb-6">
                <table className="w-full text-left border-collapse">
                    <thead className="text-[11px] font-black text-gray-500 uppercase tracking-wider border-y border-gray-200">
                        <tr>
                            <th className="py-4 px-2 w-16 text-center">No</th>
                            <th className="py-4 px-4 w-2/5">Judul Laporan PKL</th>
                            <th className="py-4 px-4">Penulis (Siswa)</th>
                            <th className="py-4 px-4 text-center">Tahun</th>
                            <th className="py-4 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {dataLaporan.length > 0 ? dataLaporan.map((l, i) => (
                            <tr key={l.ISBN || i} className="text-sm hover:bg-gray-50 transition-colors">
                                <td className="py-4 px-2 text-center text-gray-500 font-mono font-medium">{(laporanPage - 1) * 5 + i + 1}</td>
                                <td className="py-4 px-4 font-bold text-[#265F9C] leading-snug">{l.judul_koleksi}</td>
                                <td className="py-4 px-4 font-semibold text-gray-700">{l.nama_siswa_tetap || 'Anonim'}</td>
                                <td className="py-4 px-4 text-center font-mono font-bold text-gray-600">{l.tahun}</td>
                                <td className="py-4 px-4 text-center">
                                    <div className="flex justify-center gap-4">
                                        <button onClick={() => openEditModal(l)} className="text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors">EDIT</button>
                                        <button onClick={() => handleHapus(l.ISBN, l.judul_koleksi)} className="text-xs font-bold text-red-400 hover:text-red-600 transition-colors">HAPUS</button>
                                    </div>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="5" className="py-20 text-center">
                                    <div className="flex flex-col items-center justify-center text-gray-400">
                                        <svg className="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span className="italic text-sm font-medium">Laporan tidak ditemukan di database.</span>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* PAGINATION */}
            <div className="flex justify-between items-center bg-gray-50/80 p-3 rounded-lg border border-gray-100 print:hidden mt-auto">
                <button disabled={laporanPage === 1} onClick={() => setLaporanPage(laporanPage - 1)} className="px-6 py-2 bg-white border border-gray-200 rounded-md disabled:opacity-50 text-xs font-bold text-gray-600 hover:bg-gray-50 shadow-sm transition-all"> Prev </button>
                <span className="text-xs font-bold text-[#585858] tracking-widest uppercase">
                    Halaman <span className="text-[#265F9C] ml-1">{laporanPage}</span> / {laporanPagination.last_page || 1}
                </span>
                <button disabled={laporanPage >= (laporanPagination.last_page || 1)} onClick={() => setLaporanPage(laporanPage + 1)} className="px-6 py-2 bg-white border border-gray-200 rounded-md disabled:opacity-50 text-xs font-bold text-gray-600 hover:bg-gray-50 shadow-sm transition-all"> Next </button>
            </div>

            {/* MODAL POP-UP TAMBAH/EDIT */}
            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                        <div className="bg-[#265F9C] px-6 py-4 flex justify-between items-center text-white">
                            <h3 className="text-lg font-bold font-montserrat">{modalMode === 'add' ? 'Tambah Laporan PKL' : 'Ubah Laporan PKL'}</h3>
                            <button onClick={() => setIsModalOpen(false)} className="text-white/70 hover:text-white transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <form onSubmit={handleSubmitForm} className="p-6 space-y-5">
                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Judul Laporan</label>
                                <input type="text" required maxLength="25" placeholder="Maksimal 25 huruf..." className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.judul_koleksi} onChange={(e) => setFormData({...formData, judul_koleksi: e.target.value})} />
                            </div>
                            <div className="grid grid-cols-2 gap-5">
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Penulis (Nama Siswa)</label>
                                    <input type="text" required maxLength="25" placeholder="Nama penulis..." className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.pengarang} onChange={(e) => setFormData({...formData, pengarang: e.target.value})} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Tahun Laporan</label>
                                    <input type="number" required min="2000" max="2099" placeholder="Contoh: 2024" className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.tahun} onChange={(e) => setFormData({...formData, tahun: e.target.value})} />
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Upload File (PDF/Doc, Max 10MB)</label>
                                <input type="file" accept=".pdf,.doc,.docx" required={modalMode === 'add'} className="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#265F9C]/10 file:text-[#265F9C] hover:file:bg-[#265F9C]/20 transition-all cursor-pointer border border-gray-200 rounded-lg p-1" onChange={(e) => setFormData({...formData, file_laporan: e.target.files[0]})} />
                                {modalMode === 'edit' && <p className="text-[11px] text-gray-400 mt-2 font-medium">*Abaikan jika tidak ingin mengganti file laporan saat ini.</p>}
                            </div>
                            <div className="flex justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                                <button type="button" onClick={() => setIsModalOpen(false)} className="px-6 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg text-sm transition-colors">Batal</button>
                                <button type="submit" className="px-6 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white font-bold rounded-lg text-sm transition-colors shadow-md">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default LaporanPKLPanel;