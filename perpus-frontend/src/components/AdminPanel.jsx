import React, { useEffect, useState } from 'react';
import axios from 'axios';
import ManajemenKoleksiPanel from './ManajemenKoleksiPanel';
import ManajemenBukuPanel from './ManajemenBukuPanel';
import PengembalianPanel from './PengembalianPanel';
import PeminjamanPanel from './PeminjamanPanel';
import RiwayatPinjamPanel from './RiwayatPinjamPanel';
import PemusnahanPanelV2 from './PemusnahanPanelV2';
import LaporanPeminjamanGuruPanel from './LaporanPeminjamanGuruPanel';
import LaporanInventarisasiBukuBaruPanel from './LaporanInventarisasiBukuBaruPanel';
import LaporanPKLPanel from './LaporanPKLPanel';
import LaporanSiswaTerajinPanel from './LaporanSiswaTerajinPanel';
import KunjunganBulananPanel from './KunjunganBulananPanel';
import BukuTerpopulerPanel from './BukuTerpopulerPanel';
import KategoriPopulerPanel from './KategoriPopulerPanel';
import DashboardReportsPanel from './DashboardReportsPanel';

const ADMIN_ACTIVE_TAB_KEY = 'admin_active_tab';

const menuItems = [
    ['dashboard', 'Dashboard'],
    ['koleksi', 'Master Koleksi'],
    ['buku', 'Manajemen Buku'],
    ['anggota', 'Data Anggota'],
    ['laporan', 'Laporan PKL'],
    ['pengembalian', 'Pengembalian'],
    ['peminjaman', 'Peminjaman Buku'],
    ['pemusnahan', 'Pemusnahan Buku'],
    ['riwayat_pinjam', 'Riwayat Peminjaman'],
    ['laporan_peminjaman_guru', 'Peminjaman Guru'],
    ['laporan_inventarisasi_buku_baru', 'Inventarisasi Buku Baru'],
    ['laporan_siswa_terajin', 'Peringkat Siswa Terajin'],
    ['kunjungan_bulanan', 'Kunjungan Bulanan'],
    ['buku_terpopuler', 'Buku Terpopuler'],
    ['kategori_populer', 'Kategori Terpopuler'],
];

const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState(() => {
        const savedTab = localStorage.getItem(ADMIN_ACTIVE_TAB_KEY);
        return menuItems.some(([tab]) => tab === savedTab) ? savedTab : 'dashboard';
    });
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(false);
    const [dataAnggota, setDataAnggota] = useState([]);
    const [searchAnggota, setSearchAnggota] = useState('');
    const [filterRole, setFilterRole] = useState('Semua');
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const resStats = await axios.get('http://localhost:8000/api/dashboard/stats');
                setStats(resStats.data);
            } catch (error) {
                console.error(error);
            }
        };

        fetchStats();
    }, []);

    useEffect(() => {
        const fetchAnggota = async () => {
            if (activeTab !== 'anggota') return;

            setLoading(true);
            try {
                const res = await axios.get('http://localhost:8000/api/anggota', {
                    params: { page: anggotaPage, per_page: 10 },
                });
                setDataAnggota(res.data.data);
                setAnggotaPagination(res.data);
            } catch (error) {
                console.error(error);
            } finally {
                setLoading(false);
            }
        };

        fetchAnggota();
    }, [activeTab, anggotaPage]);

    useEffect(() => {
        localStorage.setItem(ADMIN_ACTIVE_TAB_KEY, activeTab);
    }, [activeTab]);

    const setTab = (tab) => {
        setActiveTab(tab);
        if (tab === 'anggota') {
            setAnggotaPage(1);
        }
    };

    const filteredAnggota = dataAnggota
        .filter((item) => {
            const matchRole = filterRole === 'Semua' || item.role === filterRole;
            const matchName = item.nama.toLowerCase().startsWith(searchAnggota.toLowerCase());
            return matchRole && matchName;
        })
        .sort((a, b) => a.nama.localeCompare(b.nama));

    return (
        <div className="min-h-screen bg-[#F6F7F9] flex font-roboto text-[#1A1A1A]">
            <aside className="w-64 h-screen sticky top-0 bg-[#265F9C] text-white flex flex-col shadow-xl print:hidden">
                <div className="p-6 pb-2 shrink-0">
                    <h2 className="font-montserrat font-bold text-xl tracking-tight text-center uppercase">Kaca Admin</h2>
                </div>

                <div className="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
                    <nav className="flex flex-col space-y-2">
                        {menuItems.map(([tab, label]) => (
                            <div
                                key={tab}
                                onClick={() => setTab(tab)}
                                className={`p-3 rounded-lg cursor-pointer transition-all ${activeTab === tab ? 'bg-white text-[#265F9C] font-bold shadow-md' : 'hover:bg-white/10'}`}
                            >
                                {label}
                            </div>
                        ))}
                    </nav>
                </div>

                <div className="p-4 border-t border-white/20 shrink-0 bg-[#265F9C]">
                    <button
                        onClick={onLogout}
                        className="w-full flex items-center justify-center gap-2 p-3 bg-red-500/20 text-red-200 font-bold hover:bg-red-500 hover:text-white rounded-lg transition-colors shadow-sm"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Keluar Sistem
                    </button>
                </div>
            </aside>

            <main className="flex-1 p-10 overflow-y-auto print:p-0 relative">
                {loading && (activeTab === 'dashboard' || activeTab === 'anggota') && (
                    <div className="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-50 flex items-center justify-center">
                        <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                )}

                {activeTab === 'dashboard' && (
                    <div>
                        <div className="grid grid-cols-1 gap-6 xl:grid-cols-3">
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
                        <DashboardReportsPanel />
                    </div>
                )}

                {activeTab === 'koleksi' && <ManajemenKoleksiPanel user={user} />}
                {activeTab === 'buku' && <ManajemenBukuPanel user={user} />}

                {activeTab === 'anggota' && (
                    <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
                        <div className="flex justify-between items-center mb-8">
                            <h1 className="text-2xl font-bold font-montserrat">Data Anggota</h1>
                            <div className="flex gap-3 flex-1 justify-end">
                                <input type="text" placeholder="Cari nama..." className="p-3 border rounded-xl text-sm w-2/3 focus:ring-2 focus:ring-[#265F9C]" value={searchAnggota} onChange={(e) => { setSearchAnggota(e.target.value); setAnggotaPage(1); }} />
                                <select className="p-3 border rounded-xl text-sm bg-gray-50" value={filterRole} onChange={(e) => { setFilterRole(e.target.value); setAnggotaPage(1); }}>
                                    <option value="Semua">Semua Role</option>
                                    <option value="Karyawan">Karyawan</option>
                                    <option value="Siswa">Siswa</option>
                                </select>
                            </div>
                        </div>
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                                <tr>
                                    <th className="p-4 w-16 text-center">No</th>
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
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                            <button disabled={anggotaPage === 1} onClick={() => setAnggotaPage(anggotaPage - 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Prev</button>
                            <span className="text-xs font-bold text-[#585858]">Halaman {anggotaPage} / {anggotaPagination.last_page || 1}</span>
                            <button disabled={anggotaPage === anggotaPagination.last_page} onClick={() => setAnggotaPage(anggotaPage + 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Next</button>
                        </div>
                    </div>
                )}

                {activeTab === 'laporan' && <LaporanPKLPanel />}
                {activeTab === 'pengembalian' && <PengembalianPanel user={user} />}
                {activeTab === 'peminjaman' && <PeminjamanPanel user={user} />}
                {activeTab === 'pemusnahan' && <PemusnahanPanelV2 user={user} />}
                {activeTab === 'riwayat_pinjam' && <RiwayatPinjamPanel user={user} />}
                {activeTab === 'laporan_peminjaman_guru' && <LaporanPeminjamanGuruPanel />}
                {activeTab === 'laporan_inventarisasi_buku_baru' && <LaporanInventarisasiBukuBaruPanel />}
                {activeTab === 'laporan_siswa_terajin' && <LaporanSiswaTerajinPanel />}
                {activeTab === 'kunjungan_bulanan' && <KunjunganBulananPanel />}
                {activeTab === 'buku_terpopuler' && <BukuTerpopulerPanel />}
                {activeTab === 'kategori_populer' && <KategoriPopulerPanel />}
            </main>
        </div>
    );
};

export default AdminPanel;
