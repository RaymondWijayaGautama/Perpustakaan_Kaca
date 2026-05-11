import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import useConfirmDialog from './useConfirmDialog';

const LaporanPKLPanel = () => {
    const { confirm, ConfirmDialog } = useConfirmDialog();
    const [dataLaporan, setDataLaporan] = useState([]);
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    
    const [searchJudul, setSearchJudul] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');
    const [loading, setLoading] = useState(false);
    const [downloadingId, setDownloadingId] = useState(null);

    // STATE MODAL FORM
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState('add');
    const [formData, setFormData] = useState({
        isbn: '', judul_koleksi: '', pengarang: '', tahun: '', file_laporan: null
    });

    const getErrorMessage = (error) => {
        const pesan = error.response?.data?.pesan;

        if (typeof pesan === 'string') {
            return pesan;
        }

        if (pesan && typeof pesan === 'object') {
            return Object.values(pesan).flat().join(' ');
        }

        return error.message || 'Terjadi kesalahan.';
    };

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

    // --- FITUR DOWNLOAD (MENGGUNAKAN GET) ---
    const handleDownload = async (isbn, judul) => {
        setDownloadingId(isbn);
        try {
            const response = await axios({
                url: `http://localhost:8000/api/laporan/download/${isbn}`,
                method: 'GET',
                responseType: 'blob',
            });

            const blob = new Blob([response.data]);
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;

            let filename = `${judul || 'laporan'}.pdf`;
            const contentDisposition = response.headers['content-disposition'];
            if (contentDisposition) {
                const match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match && match[1]) {
                    filename = match[1].replace(/['"]/g, '');
                }
            }

            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error("Download gagal:", error);
            alert(error.response?.data?.pesan || "Gagal mengunduh file. Pastikan file tersedia.");
        } finally {
            setDownloadingId(null);
        }
    };

    // HAPUS DATA
    const handleHapus = async (id, judul) => {
        const yakin = await confirm({
            title: 'Hapus Laporan',
            message: `Yakin ingin menghapus laporan "${judul}"?`,
            confirmLabel: 'Ya, Hapus',
            tone: 'danger',
        });
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
            alert("Gagal menyimpan: " + getErrorMessage(error));
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
                    <button onClick={openAddModal} className="px-6 py-2.5 bg-[#00A651] hover:bg-[#008f45] text-white rounded-lg text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Laporan
                    </button>

                    <div className="relative w-full xl:w-64">
                        <input type="text" placeholder="Cari Judul..." className="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#265F9C] focus:border-transparent transition-all" value={searchJudul} onChange={(e) => { setSearchJudul(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div className="relative w-full xl:w-56">
                        <input type="text" placeholder="Filter Penulis..." className="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#00A651] focus:border-transparent transition-all" value={filterPenulis} onChange={(e) => { setFilterPenulis(e.target.value); setLaporanPage(1); }} />
                        <div className="absolute left-3 top-2.5 text-[#00A651]">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {/* TABLE AREA */}
            <div className="w-full mb-6 overflow-x-auto">
                <table className="w-full text-left border-collapse min-w-[600px]">
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
                                    <div className="flex justify-center items-center gap-2">
                                        <button 
                                            onClick={() => handleDownload(l.ISBN, l.judul_koleksi)} 
                                            disabled={downloadingId === l.ISBN}
                                            className="p-2 bg-green-50 text-[#00A651] rounded-lg hover:bg-[#00A651] hover:text-white transition-all shadow-sm group disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Download Laporan"
                                        >
                                            {downloadingId === l.ISBN ? (
                                                <div className="w-5 h-5 border-2 border-[#00A651] border-t-transparent rounded-full animate-spin"></div>
                                            ) : (
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            )}
                                        </button>

                                        <button 
                                            onClick={() => openEditModal(l)} 
                                            className="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                            title="Ubah Laporan"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>

                                        <button 
                                            onClick={() => handleHapus(l.ISBN, l.judul_koleksi)} 
                                            className="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                            title="Hapus Laporan"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="5" className="py-20 text-center text-gray-400 italic">Laporan tidak ditemukan.</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* PAGINATION */}
            <div className="flex justify-between items-center bg-gray-50/80 p-3 rounded-lg border border-gray-100 mt-auto">
                <button disabled={laporanPage === 1} onClick={() => setLaporanPage(laporanPage - 1)} className="px-6 py-2 bg-white border border-gray-200 rounded-md disabled:opacity-50 text-xs font-bold text-gray-600 hover:bg-gray-50 shadow-sm transition-all"> Prev </button>
                <span className="text-xs font-bold text-[#585858] tracking-widest uppercase">
                    Halaman <span className="text-[#265F9C] ml-1">{laporanPage}</span> / {laporanPagination.last_page || 1}
                </span>
                <button disabled={laporanPage >= (laporanPagination.last_page || 1)} onClick={() => setLaporanPage(laporanPage + 1)} className="px-6 py-2 bg-white border border-gray-200 rounded-md disabled:opacity-50 text-xs font-bold text-gray-600 hover:bg-gray-50 shadow-sm transition-all"> Next </button>
            </div>

            {/* MODAL FORM TAMBAH/UBAH */}
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
                                <input type="text" required placeholder="Judul..." className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.judul_koleksi} onChange={(e) => setFormData({...formData, judul_koleksi: e.target.value})} />
                            </div>
                            <div className="grid grid-cols-2 gap-5">
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Penulis</label>
                                    <input type="text" required placeholder="Nama..." className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.pengarang} onChange={(e) => setFormData({...formData, pengarang: e.target.value})} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Tahun</label>
                                    <input type="number" required placeholder="2024" className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all" value={formData.tahun} onChange={(e) => setFormData({...formData, tahun: e.target.value})} />
                                </div>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Upload File (PDF/Doc)</label>
                                <input type="file" accept=".pdf,.doc,.docx" required={modalMode === 'add'} className="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#265F9C]/10 file:text-[#265F9C] border border-gray-200 rounded-lg p-1" onChange={(e) => setFormData({...formData, file_laporan: e.target.files[0]})} />
                            </div>
                            <div className="flex justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                                <button type="button" onClick={() => setIsModalOpen(false)} className="px-6 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg text-sm transition-colors">Batal</button>
                                <button type="submit" className="px-6 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white font-bold rounded-lg text-sm transition-colors shadow-md">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
            <ConfirmDialog />
        </div>
    );
};

export default LaporanPKLPanel;
