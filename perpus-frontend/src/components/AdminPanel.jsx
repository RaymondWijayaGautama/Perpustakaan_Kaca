import React, { useState, useEffect } from 'react';
import axios from 'axios';
<<<<<<< HEAD
import ManajemenKoleksiPanel from './ManajemenKoleksiPanel';
import ManajemenBukuPanel from './ManajemenBukuPanel';
import PengembalianPanel from './PengembalianPanel';
import PeminjamanPanel from './PeminjamanPanel';
import RiwayatPinjamPanel from './RiwayatPinjamPanel';
import PemusnahanPanelV2 from './PemusnahanPanelV2';
import LaporanPeminjamanBulananPanel from './LaporanPeminjamanBulananPanel';
import LaporanPeminjamanGuruPanel from './LaporanPeminjamanGuruPanel';
import LaporanDistribusiKunjunganKelasPanel from './LaporanDistribusiKunjunganKelasPanel';
import LaporanDistribusiKunjunganHariPanel from './LaporanDistribusiKunjunganHariPanel';
import LaporanInventarisasiBukuBaruPanel from './LaporanInventarisasiBukuBaruPanel';
import LaporanPKLPanel from './LaporanPKLPanel';
import LaporanSiswaTerajinPanel from './LaporanSiswaTerajinPanel';
import KunjunganBulananPanel from './KunjunganBulananPanel';
import BukuTerpopulerPanel from './BukuTerpopulerPanel';
import KategoriPopulerPanel from './KategoriPopulerPanel';
=======
import JamDatang from './JamDatang'; 
import JamPulang from './JamPulang';
import KalkulasiDendaBukuRusak from './KalkulasiDendaBukuRusak';
>>>>>>> checkin

const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [isDistribusiMenuOpen, setIsDistribusiMenuOpen] = useState(false);
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(false);

<<<<<<< HEAD
    const [dataAnggota, setDataAnggota] = useState([]);
=======
    // State untuk modal denda
    const [selectedBukuId, setSelectedBukuId] = useState(null);

    // Data Lists
    const [dataAnggota, setDataAnggota] = useState([]);
    const [books, setBooks] = useState([]);
    const [dataLaporan, setDataLaporan] = useState([]);

    // State Anggota
>>>>>>> checkin
    const [searchAnggota, setSearchAnggota] = useState('');
    const [filterRole, setFilterRole] = useState('Semua');
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});

<<<<<<< HEAD
    // Fetch Stats untuk Dashboard
=======
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

>>>>>>> checkin
    useEffect(() => {
        const fetchStats = async () => {
            try {
                const resStats = await axios.get('http://localhost:8000/api/dashboard/stats');
                setStats(resStats.data);
            } catch (error) { console.error(error); }
        };
        fetchStats();
    }, []);

    // Fetch Data Anggota
    useEffect(() => {
        const fetchAnggota = async () => {
            if (activeTab !== 'anggota') return;
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
        fetchAnggota();
    }, [activeTab, anggotaPage]);

<<<<<<< HEAD
    const filteredAnggota = dataAnggota
        .filter(item => {
            const matchRole = filterRole === 'Semua' || item.role === filterRole;
            const matchName = item.nama.toLowerCase().startsWith(searchAnggota.toLowerCase());
            return matchRole && matchName;
        })
        .sort((a, b) => a.nama.localeCompare(b.nama));

    useEffect(() => {
        if (activeTab.includes('laporan_distribusi')) {
            setIsDistribusiMenuOpen(true);
        }
    }, [activeTab]);

    return (
        <div className="min-h-screen bg-[#F6F7F9] flex font-roboto text-[#1A1A1A]">
            
            {/* SIDEBAR */}
            <aside className="w-64 h-screen sticky top-0 bg-[#265F9C] text-white flex flex-col shadow-xl print:hidden">
                {/* BLOK LOGO */}
                <div className="p-6 pb-2 shrink-0">
                    <h2 className="font-montserrat font-bold text-xl tracking-tight text-center uppercase">Kaca Admin</h2>
                </div>

                {/* BLOK MENU BISA DI-SCROLL */}
                <div className="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
                    <nav className="flex flex-col space-y-2">
                        <div onClick={() => setActiveTab('dashboard')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'dashboard' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Dashboard</div>
                        <div onClick={() => setActiveTab('koleksi')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'koleksi' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Master Koleksi</div>
                        <div onClick={() => setActiveTab('buku')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'buku' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Manajemen Buku</div>
                        <div onClick={() => { setActiveTab('anggota'); setAnggotaPage(1); }} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'anggota' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Data Anggota</div>
                        <div onClick={() => setActiveTab('laporan')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'laporan' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Laporan PKL</div>
                        <div onClick={() => setActiveTab('pengembalian')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'pengembalian' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Pengembalian</div>
                        <div onClick={() => setActiveTab('peminjaman')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'peminjaman' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Peminjaman Buku</div>
                        <div onClick={() => setActiveTab('pemusnahan')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'pemusnahan' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Pemusnahan Buku</div>
                        <div onClick={() => setActiveTab('riwayat_pinjam')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'riwayat_pinjam' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Riwayat Peminjaman</div>
                        <div onClick={() => setActiveTab('laporan_peminjaman_bulanan')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'laporan_peminjaman_bulanan' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Statistik Peminjaman</div>
                        <div onClick={() => setActiveTab('laporan_peminjaman_guru')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'laporan_peminjaman_guru' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Peminjaman Guru</div>
                        <div onClick={() => setActiveTab('laporan_inventarisasi_buku_baru')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'laporan_inventarisasi_buku_baru' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Inventarisasi Buku Baru</div>
                        <div onClick={() => setActiveTab('laporan_siswa_terajin')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'laporan_siswa_terajin' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Peringkat Siswa Terajin</div>
                        <div onClick={() => setActiveTab('kunjungan_bulanan')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'kunjungan_bulanan' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Kunjungan Bulanan</div>
                        <div onClick={() => setActiveTab('buku_terpopuler')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'buku_terpopuler' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Buku Terpopuler</div>
                        <div onClick={() => setActiveTab('kategori_populer')} className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === 'kategori_populer' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}>Kategori Terpopuler</div>
                        
                        <div className={`rounded-lg transition-all overflow-hidden ${activeTab === 'laporan_distribusi_kunjungan_kelas' || activeTab === 'laporan_distribusi_kunjungan_hari' || isDistribusiMenuOpen ? 'bg-white/10' : ''}`}>
                            <div
                                onClick={() => setIsDistribusiMenuOpen((current) => !current)}
                                className={`p-3 cursor-pointer transition-all flex items-center justify-between rounded-lg ${activeTab === 'laporan_distribusi_kunjungan_kelas' || activeTab === 'laporan_distribusi_kunjungan_hari' ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}
                            >
                                <span>Distribusi Kunjungan</span>
                                <span className={`text-xs transition-transform ${isDistribusiMenuOpen ? 'rotate-180' : ''}`}>▼</span>
                            </div>
                            {isDistribusiMenuOpen && (
                                <div className="px-2 pt-1 pb-2 space-y-1 text-sm">
                                    <div onClick={() => setActiveTab('laporan_distribusi_kunjungan_kelas')} className={`ml-3 p-2 rounded-lg cursor-pointer ${activeTab === 'laporan_distribusi_kunjungan_kelas' ? 'bg-white text-[#265F9C] font-bold shadow-sm' : 'hover:bg-white/10'}`}>Berdasarkan Kelas</div>
                                    <div onClick={() => setActiveTab('laporan_distribusi_kunjungan_hari')} className={`ml-3 p-2 rounded-lg cursor-pointer ${activeTab === 'laporan_distribusi_kunjungan_hari' ? 'bg-white text-[#265F9C] font-bold shadow-sm' : 'hover:bg-white/10'}`}>Berdasarkan Hari</div>
                                </div>
                            )}
                        </div>
                    </nav>
                </div>

                {/* BLOK TOMBOL KELUAR */}
                <div className="p-4 border-t border-white/20 shrink-0 bg-[#265F9C]">
                    <button 
                        onClick={onLogout} 
                        className="w-full flex items-center justify-center gap-2 p-3 bg-red-500/20 text-red-200 font-bold hover:bg-red-500 hover:text-white rounded-lg transition-colors shadow-sm"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar Sistem
                    </button>
                </div>
            </aside>

            {/* MAIN CONTENT */}
            <main className="flex-1 p-10 overflow-y-auto print:p-0 relative">
                {loading && (activeTab === 'dashboard' || activeTab === 'anggota') && (
                    <div className="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-50 flex items-center justify-center">
                        <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                )}

                {activeTab === 'dashboard' && (
                    <>
                        {/* 3 Kotak KPI */}
                        <div className="grid grid-cols-3 gap-6">
                            <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#265F9C]">
                                <h3 className="text-[#585858] font-bold text-xs uppercase font-montserrat">Total Koleksi Buku</h3>
                                <p className="text-5xl font-bold mt-2 text-[#265F9C]">{stats.total_buku.toLocaleString('id-ID')}</p>
                            </div>
                            <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#2E7D32]">
                                <h3 className="text-[#585858] font-bold text-xs uppercase font-montserrat">Total Anggota</h3>
                                <p className="text-5xl font-bold mt-2 text-[#2E7D32]">{stats.total_siswa.toLocaleString('id-ID')}</p>
                            </div>
                            <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#EDA60F]">
                                <h3 className="text-[#585858] font-bold text-xs uppercase font-montserrat">Arsip Laporan PKL</h3>
                                <p className="text-5xl font-bold mt-2 text-[#EDA60F]">{stats.total_laporan.toLocaleString('id-ID')}</p>
                            </div>
                        </div>

                        {/* --- DASHBOARD POWER BI --- */}
                        <div className="w-full mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style={{ height: '700px' }}>
                            <iframe
                                title="Dashboard_Perpus_Kaca"
                                className="w-full h-full border-none"
                                src="https://app.powerbi.com/reportEmbed?reportId=59b6f7c9-e7b6-4559-b42a-01565c6c929f&autoAuth=true&navContentPaneEnabled=false&filterPaneEnabled=false"
                                allowFullScreen={true}
                            ></iframe>
                            
                        </div>
                        {/* ---------------------------- */}
                    </>
                )}
                
                {/* PANEL COMPONENTS */}
                {activeTab === 'koleksi' && <ManajemenKoleksiPanel user={user} />}
                {activeTab === 'buku' && <ManajemenBukuPanel user={user} />}
                
                {activeTab === 'anggota' && (
                    <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
                        <div className="flex justify-between items-center mb-8">
                            <h1 className="text-2xl font-bold font-montserrat">Data Anggota</h1>
                            <div className="flex gap-3 flex-1 justify-end">
                                <input type="text" placeholder="Cari nama..." className="p-3 border rounded-xl text-sm w-2/3 focus:ring-2 focus:ring-[#265F9C]" value={searchAnggota} onChange={(e) => {setSearchAnggota(e.target.value); setAnggotaPage(1);}} />
                                <select className="p-3 border rounded-xl text-sm bg-gray-50" value={filterRole} onChange={(e) => {setFilterRole(e.target.value); setAnggotaPage(1);}}>
                                    <option value="Semua">Semua Role</option>
                                    <option value="Karyawan">Karyawan</option>
                                    <option value="Siswa">Siswa</option>
                                </select>
                            </div>
                        </div>
=======
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
>>>>>>> checkin
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                                <tr>
                                    <th className="p-4 w-16 text-center">No</th>
<<<<<<< HEAD
                                    <th className="p-4">NISN/NIP</th>
                                    <th className="p-4">Nama</th>
                                    <th className="p-4 text-center">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredAnggota.map((a, i) => (
                                    <tr key={i} className="border-b text-sm hover:bg-blue-50/40">
                                        <td className="p-4 text-center text-[#7D7D7E]">{(anggotaPage - 1) * 10 + i + 1}</td>
                                        <td className="p-4 font-mono text-xs">{a.identitas}</td>
                                        <td className="p-4 font-bold">{a.nama}</td>
                                        <td className="p-4 text-center">
                                            <span className={`px-3 py-1 rounded-full text-[10px] font-black border ${a.role === 'Karyawan' ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-blue-50 text-[#265F9C] border-blue-100'}`}>{a.role}</span>
=======
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
>>>>>>> checkin
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        
                        {/* Pagination */}
                        <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
<<<<<<< HEAD
                            <button disabled={anggotaPage === 1} onClick={() => setAnggotaPage(anggotaPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Prev</button>
                            <span className="text-xs font-bold text-[#585858]">Halaman {anggotaPage} / {anggotaPagination.last_page || 1}</span>
                            <button disabled={anggotaPage === anggotaPagination.last_page} onClick={() => setAnggotaPage(anggotaPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Next</button>
=======
                            <button disabled={bookPage === 1} onClick={() => setBookPage(bookPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm hover:bg-gray-100">Prev</button>
                            <span className="text-xs font-bold text-[#585858] uppercase tracking-widest">Halaman {bookPage}</span>
                            <button disabled={bookPage === bookPagination.last_page} onClick={() => setBookPage(bookPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm hover:bg-gray-100">Next</button>
>>>>>>> checkin
                        </div>
                    </div>
                )}

<<<<<<< HEAD
                {activeTab === 'laporan' && <LaporanPKLPanel />} 
                {activeTab === 'pengembalian' && <PengembalianPanel user={user} />}
                {activeTab === 'peminjaman' && <PeminjamanPanel user={user} />}
                {activeTab === 'pemusnahan' && <PemusnahanPanelV2 user={user} />}
                {activeTab === 'riwayat_pinjam' && <RiwayatPinjamPanel user={user} />}
                {activeTab === 'laporan_peminjaman_bulanan' && <LaporanPeminjamanBulananPanel />}
                {activeTab === 'laporan_peminjaman_guru' && <LaporanPeminjamanGuruPanel />}
                {activeTab === 'laporan_inventarisasi_buku_baru' && <LaporanInventarisasiBukuBaruPanel />}
                {activeTab === 'laporan_distribusi_kunjungan_kelas' && <LaporanDistribusiKunjunganKelasPanel />}
                {activeTab === 'laporan_distribusi_kunjungan_hari' && <LaporanDistribusiKunjunganHariPanel />}
                {activeTab === 'laporan_siswa_terajin' && <LaporanSiswaTerajinPanel />}
                {activeTab === 'kunjungan_bulanan' && <KunjunganBulananPanel />}
                {activeTab === 'buku_terpopuler' && <BukuTerpopulerPanel />}
                {activeTab === 'kategori_populer' && <KategoriPopulerPanel />}
=======
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
>>>>>>> checkin
            </main>
        </div>
    );
};

export default AdminPanel;