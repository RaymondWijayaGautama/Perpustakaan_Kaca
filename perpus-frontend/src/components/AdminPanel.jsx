import React, { useState, useEffect } from 'react';
import axios from 'axios';
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
import BukuTerpopulerPanel from './BukuTerpopulerPanel'
import KategoriPopulerPanel from './KategoriPopulerPanel';
const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [isDistribusiMenuOpen, setIsDistribusiMenuOpen] = useState(false);
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(false);

    const [dataAnggota, setDataAnggota] = useState([]);
    const [searchAnggota, setSearchAnggota] = useState('');
    const [filterRole, setFilterRole] = useState('Semua');
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});

    // Fetch Stats untuk Dashboard
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
            <aside className="w-64 bg-[#265F9C] text-white p-6 flex flex-col shadow-xl print:hidden">
                <h2 className="font-montserrat font-bold text-xl mb-10 tracking-tight text-center uppercase">Kaca Admin</h2>
                <nav className="flex-1 space-y-2">
                    <div onClick={() => setActiveTab('dashboard')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'dashboard' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Dashboard</div>
                    <div onClick={() => setActiveTab('buku')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'buku' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Manajemen Buku</div>
                    <div onClick={() => { setActiveTab('anggota'); setAnggotaPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'anggota' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Data Anggota</div>
                    <div onClick={() => setActiveTab('laporan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Laporan PKL</div>
                    <div onClick={() => setActiveTab('pengembalian')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'pengembalian' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Pengembalian</div>
                    <div onClick={() => setActiveTab('peminjaman')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'peminjaman' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Peminjaman Buku</div>
                    <div onClick={() => setActiveTab('pemusnahan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'pemusnahan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Pemusnahan Buku</div>
                    <div onClick={() => setActiveTab('riwayat_pinjam')}className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'riwayat_pinjam' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Riwayat Peminjaman</div>
                    <div onClick={() => setActiveTab('laporan_peminjaman_bulanan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan_peminjaman_bulanan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Statistik Peminjaman</div>
                    <div onClick={() => setActiveTab('laporan_peminjaman_guru')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan_peminjaman_guru' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Peminjaman Guru</div>
                    <div onClick={() => setActiveTab('laporan_inventarisasi_buku_baru')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan_inventarisasi_buku_baru' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Inventarisasi Buku Baru</div>
                    <div onClick={() => setActiveTab('laporan_siswa_terajin')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan_siswa_terajin' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Peringkat Siswa Terajin</div>
                    <div onClick={() => setActiveTab('kunjungan_bulanan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'kunjungan_bulanan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Kunjungan Bulanan</div>
                    <div onClick={() => setActiveTab('buku_terpopuler')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'buku_terpopuler' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Buku Terpopuler</div>
                    <div onClick={() => setActiveTab('kategori_populer')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'kategori_populer' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Kategori Terpopuler</div>
                    {/* <div onClick={() => setActiveTab('manajemen_buku')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'manajemen_buku' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Manajemen Buku</div> */}
                    <div className={`rounded transition-all overflow-hidden ${activeTab === 'laporan_distribusi_kunjungan_kelas' || activeTab === 'laporan_distribusi_kunjungan_hari' || isDistribusiMenuOpen ? 'bg-white/10' : ''}`}>
                        <div
                            onClick={() => setIsDistribusiMenuOpen((current) => !current)}
                            className={`p-3 cursor-pointer transition-all flex items-center justify-between ${activeTab === 'laporan_distribusi_kunjungan_kelas' || activeTab === 'laporan_distribusi_kunjungan_hari' ? 'font-bold border-l-4 border-white bg-white/20' : 'hover:bg-white/10'}`}
                        >
                            <span>Laporan Distribusi Kunjungan</span>
                            <span className={`text-xs transition-transform ${isDistribusiMenuOpen ? 'rotate-180' : ''}`}>▼</span>
                        </div>
                        {isDistribusiMenuOpen && (
                            <div className="px-2 pb-2 space-y-1 bg-white/5 text-sm">
                                <div onClick={() => setActiveTab('laporan_distribusi_kunjungan_kelas')} className={`ml-3 p-2 rounded-lg cursor-pointer ${activeTab === 'laporan_distribusi_kunjungan_kelas' ? 'bg-white text-[#265F9C] font-bold' : 'hover:bg-white/10'}`}>Berdasarkan Kelas</div>
                                <div onClick={() => setActiveTab('laporan_distribusi_kunjungan_hari')} className={`ml-3 p-2 rounded-lg cursor-pointer ${activeTab === 'laporan_distribusi_kunjungan_hari' ? 'bg-white text-[#265F9C] font-bold' : 'hover:bg-white/10'}`}>Berdasarkan Hari</div>
                            </div>
                        )}
                    </div>
                </nav>
                <button onClick={onLogout} className="mt-auto p-2 text-red-200 font-bold hover:text-white transition-colors text-left">Keluar Sistem</button>
            </aside>

            {/* MAIN CONTENT */}
            <main className="flex-1 p-10 overflow-y-auto print:p-0 relative">
                {/* Loader hanya untuk tab yang dikelola AdminPanel langsung */}
                {loading && (activeTab === 'dashboard' || activeTab === 'anggota') && (
                    <div className="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-50 flex items-center justify-center">
                        <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                    </div>
                )}

                {activeTab === 'dashboard' && (
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
                )}
                
                {/* PANEL COMPONENTS */}
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
                            <button disabled={anggotaPage === 1} onClick={() => setAnggotaPage(anggotaPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Prev</button>
                            <span className="text-xs font-bold text-[#585858]">Halaman {anggotaPage} / {anggotaPagination.last_page || 1}</span>
                            <button disabled={anggotaPage === anggotaPagination.last_page} onClick={() => setAnggotaPage(anggotaPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 font-bold shadow-sm">Next</button>
                        </div>
                    </div>
                )}

                {/* Tab Laporan PKL yang sudah dipisah */}
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
                {activeTab === 'manajemen_buku' && <ManajemenBukuPanel user={user} />}
            </main>
        </div>
    );
};

export default AdminPanel;