import React, { useState, useEffect } from 'react';
import axios from 'axios';
import JamDatang from './JamDatang'; 
import JamPulang from './JamPulang';
import KalkulasiDendaBukuRusak from './KalkulasiDendaBukuRusak';

const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(true);

    // State untuk modal denda
    const [selectedBukuId, setSelectedBukuId] = useState(null);

    // Data Lists
    const [dataAnggota, setDataAnggota] = useState([]);
    const [books, setBooks] = useState([]);
    const [dataLaporan, setDataLaporan] = useState([]);

    // State Anggota
    const [searchAnggota, setSearchAnggota] = useState('');
    const [filterRole, setFilterRole] = useState('Semua');
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});

    // State Buku
    const [bookSearch, setBookSearch] = useState('');
    const [bookSortBy, setBookSortBy] = useState('judul_koleksi');
    const [bookPage, setBookPage] = useState(1);
    const [bookPagination, setBookPagination] = useState({});

    // State Laporan
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    const [filterTahun, setFilterTahun] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');

    const fetchStats = async () => {
        try {
            const resStats = await axios.get('http://localhost:8000/api/dashboard/stats');
            setStats(resStats.data);
        } catch (error) { console.error(error); }
    };

    const fetchAnggota = async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/anggota', {
                params: { page: anggotaPage, per_page: 10 }
            });
            setDataAnggota(res.data.data);
            setAnggotaPagination(res.data);
        } catch (error) { console.error(error); }
        finally { setLoading(false); }
    };

    const fetchBooks = async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/buku', {
                params: { search: bookSearch, sort_by: bookSortBy, page: bookPage, per_page: 10 }
            });
            setBooks(res.data.data);
            setBookPagination(res.data);
        } catch (error) { console.error(error); }
        finally { setLoading(false); }
    };

    const fetchLaporan = async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/laporan', {
                params: { page: laporanPage, tahun: filterTahun, penulis: filterPenulis, per_page: 10 }
            });
            setDataLaporan(res.data.data);
            setLaporanPagination(res.data);
        } catch (error) { console.error(error); }
        finally { setLoading(false); }
    };

    useEffect(() => {
        fetchStats();
    }, []);

    useEffect(() => {
        if (activeTab === 'anggota') fetchAnggota();
        if (activeTab === 'buku') fetchBooks();
        if (activeTab === 'laporan') fetchLaporan();
    }, [activeTab, anggotaPage, bookPage, bookSortBy, laporanPage]);

    return (
        <div className="min-h-screen bg-[#F6F7F9] flex font-roboto text-[#1A1A1A]">
            
            {/* MODAL OVERLAY - Tetap mempertahankan animasi backdrop blur */}
            {selectedBukuId !== null && (
                <div className="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="relative animate-fadeIn">
                        <button 
                            onClick={() => setSelectedBukuId(null)} 
                            className="absolute -top-10 right-0 text-white font-bold px-3 py-1 bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            Tutup [X]
                        </button>
                        <KalkulasiDendaBukuRusak 
                            bukuId={selectedBukuId} 
                            onkSukses={() => {
                                fetchBooks(); 
                                setTimeout(() => setSelectedBukuId(null), 1500);
                            }} 
                        />
                    </div>
                </div>
            )}

            {/* SIDEBAR - Tampilan Persis Kode Lama */}
            <aside className="w-64 bg-[#265F9C] text-white p-6 flex flex-col shadow-xl print:hidden">
                <h2 className="font-montserrat font-bold text-xl mb-10 tracking-tight text-center uppercase">Kaca Admin</h2>
                <nav className="flex-1 space-y-2">
                    <div onClick={() => setActiveTab('dashboard')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'dashboard' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Dashboard</div>
                    
                    <div className="pt-4 pb-1 px-3 text-[10px] font-black opacity-50 uppercase tracking-widest">Presensi</div>
                    <div onClick={() => setActiveTab('jam_datang')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'jam_datang' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Jam Datang</div>
                    <div onClick={() => setActiveTab('jam_pulang')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'jam_pulang' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Jam Pulang</div>
                    
                    <div className="pt-4 pb-1 px-3 text-[10px] font-black opacity-50 uppercase tracking-widest">Manajemen</div>
                    <div onClick={() => { setActiveTab('buku'); setBookPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'buku' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Manajemen Buku</div>
                    <div onClick={() => { setActiveTab('anggota'); setAnggotaPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'anggota' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Data Anggota</div>
                    <div onClick={() => { setActiveTab('laporan'); setLaporanPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Laporan PKL</div>
                </nav>
                <button onClick={onLogout} className="mt-auto p-2 text-red-200 font-bold hover:text-white transition-colors text-left">Keluar Sistem</button>
            </aside>

            {/* KONTEN UTAMA */}
            <main className="flex-1 p-10 overflow-y-auto print:p-0">
                
                {activeTab === 'dashboard' && (
                    <div className="grid grid-cols-3 gap-6 animate-fadeIn">
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#265F9C]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Total Koleksi Buku</h3>
                            <p className="text-5xl font-bold mt-2 text-[#265F9C] font-montserrat">{stats.total_buku.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#2E7D32]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Total Anggota</h3>
                            <p className="text-5xl font-bold mt-2 text-[#2E7D32] font-montserrat">{stats.total_siswa.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#EDA60F]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Arsip Laporan PKL</h3>
                            <p className="text-5xl font-bold mt-2 text-[#EDA60F] font-montserrat">{(stats.total_laporan || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                )}

                {activeTab === 'jam_datang' && <div className="flex justify-center pt-10 animate-fadeIn"><JamDatang /></div>}
                {activeTab === 'jam_pulang' && <div className="flex justify-center pt-10 animate-fadeIn"><JamPulang /></div>}

                {activeTab === 'buku' && (
                    <div className="bg-white rounded-xl shadow p-6 border border-gray-100 animate-fadeIn">
                        <div className="flex justify-between items-center mb-8">
                            <h1 className="text-2xl font-bold font-montserrat">Manajemen Buku</h1>
                            <div className="flex gap-3 flex-1 justify-end">
                                <input 
                                    type="text" placeholder="Cari Judul..." 
                                    className="p-3 border rounded-xl text-sm outline-none w-2/3 focus:ring-2 focus:ring-[#265F9C]"
                                    value={bookSearch} onChange={(e) => setBookSearch(e.target.value)}
                                    onKeyDown={(e) => e.key === 'Enter' && fetchBooks()}
                                />
                            </div>
                        </div>
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                                <tr>
                                    <th className="p-4 w-16 text-center">No</th>
                                    <th className="p-4">Judul</th>
                                    <th className="p-4">ISBN</th>
                                    <th className="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="font-roboto">
                                {books.map((b, i) => (
                                    <tr key={b.ISBN || b.isbn || i} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                        <td className="p-4 text-center text-[#7D7D7E]">{(bookPage - 1) * 10 + i + 1}</td>
                                        <td className="p-4 font-bold text-[#1A1A1A]">{b.judul_koleksi}</td>
                                        <td className="p-4 text-[#585858] font-mono">{b.ISBN || b.isbn || 'N/A'}</td>
                                        <td className="p-4 text-center">
                                            <button 
                                                type="button"
                                                onClick={() => {
                                                    // PERBAIKAN: Mengambil ISBN karena di database Anda ini yang tersedia
                                                    const uniqueISBN = b.ISBN || b.isbn;
                                                    console.log("Membuka denda untuk ISBN:", uniqueISBN);
                                                    if(uniqueISBN) {
                                                        setSelectedBukuId(uniqueISBN);
                                                    } else {
                                                        alert("Error: ISBN Buku tidak ditemukan di database!");
                                                    }
                                                }}
                                                className="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase border border-red-100 hover:bg-red-600 hover:text-white transition-all cursor-pointer"
                                            >
                                                Lapor Rusak
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        
                        {/* Pagination */}
                        <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                            <button disabled={bookPage === 1} onClick={() => setBookPage(bookPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm hover:bg-gray-100">Prev</button>
                            <span className="text-xs font-bold text-[#585858] uppercase tracking-widest">Halaman {bookPage}</span>
                            <button disabled={bookPage === bookPagination.last_page} onClick={() => setBookPage(bookPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm hover:bg-gray-100">Next</button>
                        </div>
                    </div>
                )}

                {activeTab === 'anggota' && (
                    <div className="bg-white rounded-xl shadow p-10 text-center animate-fadeIn border border-gray-100">
                        <h2 className="text-xl font-bold text-gray-800">Data Anggota Perpustakaan</h2>
                        <p className="text-gray-500 mt-2">Menampilkan {dataAnggota.length} anggota aktif pada halaman ini.</p>
                        {/* Anda bisa menambahkan tabel anggota di sini jika diperlukan */}
                    </div>
                )}

                {activeTab === 'laporan' && (
                    <div className="bg-white rounded-xl shadow p-10 text-center animate-fadeIn border border-gray-100">
                        <h2 className="text-xl font-bold text-gray-800">Laporan PKL Siswa</h2>
                        <p className="text-gray-500 mt-2">Daftar arsip laporan yang telah diunggah.</p>
                    </div>
                )}
            </main>
        </div>
    );
};

export default AdminPanel;