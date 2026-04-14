import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ManajemenBukuPanel from './ManajemenBukuPanel';
import PengembalianPanel from './PengembalianPanel';
import PeminjamanPanel from './PeminjamanPanel';
import RiwayatPinjamPanel from './RiwayatPinjamPanel';

const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(false);

    const [dataAnggota, setDataAnggota] = useState([]);
    const [dataLaporan, setDataLaporan] = useState([]);

    const [searchAnggota, setSearchAnggota] = useState('');
    const [filterRole, setFilterRole] = useState('Semua');
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    const [filterTahun, setFilterTahun] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const resStats = await axios.get('http://localhost:8000/api/dashboard/stats');
                setStats(resStats.data);
            } catch (error) { console.error(error); }
        };
        fetchStats();
    }, []);

    useEffect(() => {
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

        if (activeTab === 'anggota') fetchAnggota();
        if (activeTab === 'laporan') fetchLaporan();
    }, [activeTab, anggotaPage, laporanPage, filterTahun, filterPenulis]);

    const filteredAnggota = dataAnggota
        .filter(item => {
            const matchRole = filterRole === 'Semua' || item.role === filterRole;
            const matchName = item.nama.toLowerCase().startsWith(searchAnggota.toLowerCase());
            return matchRole && matchName;
        })
        .sort((a, b) => a.nama.localeCompare(b.nama));

    return (
        <div className="min-h-screen bg-[#F6F7F9] flex font-roboto text-[#1A1A1A]">
            <aside className="w-64 bg-[#265F9C] text-white p-6 flex flex-col shadow-xl print:hidden">
                <h2 className="font-montserrat font-bold text-xl mb-10 tracking-tight text-center uppercase">Kaca Admin</h2>
                <nav className="flex-1 space-y-2">
                    <div onClick={() => setActiveTab('dashboard')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'dashboard' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Dashboard</div>
                    <div onClick={() => setActiveTab('buku')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'buku' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Manajemen Buku</div>
                    <div onClick={() => { setActiveTab('anggota'); setAnggotaPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'anggota' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Data Anggota</div>
                    <div onClick={() => { setActiveTab('laporan'); setLaporanPage(1); }} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Laporan PKL</div>
                    <div onClick={() => setActiveTab('pengembalian')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'pengembalian' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Pengembalian</div>
                    <div onClick={() => setActiveTab('peminjaman')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'peminjaman' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Peminjaman Buku</div>
                    <div onClick={() => setActiveTab('riwayat_pinjam')}className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'riwayat_pinjam' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Riwayat Peminjaman</div>
                </nav>
                <button onClick={onLogout} className="mt-auto p-2 text-red-200 font-bold hover:text-white transition-colors text-left">Keluar Sistem</button>
            </aside>
            <main className="flex-1 p-10 overflow-y-auto print:p-0 relative">
                {loading && (
                    <div className="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-50 flex items-center justify-center">
                        <div className="flex flex-col items-center">
                            <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                            <p className="mt-4 text-[#265F9C] font-bold animate-pulse">Memuat Data...</p>
                        </div>
                    </div>
                )}
                {activeTab === 'dashboard' && (
                    <div className="grid grid-cols-3 gap-6">
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#265F9C] transition-transform hover:scale-[1.02]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Total Koleksi Buku</h3>
                            <p className="text-5xl font-bold mt-2 text-[#265F9C] font-montserrat">{stats.total_buku.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#2E7D32] transition-transform hover:scale-[1.02]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Total Anggota</h3>
                            <p className="text-5xl font-bold mt-2 text-[#2E7D32] font-montserrat">{stats.total_siswa.toLocaleString('id-ID')}</p>
                        </div>
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-[#EDA60F] transition-transform hover:scale-[1.02]">
                            <h3 className="text-[#585858] font-bold text-xs uppercase tracking-wider font-montserrat">Arsip Laporan PKL</h3>
                            <p className="text-5xl font-bold mt-2 text-[#EDA60F] font-montserrat">{(stats.total_laporan || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                )}
                
                {activeTab === 'buku' && <ManajemenBukuPanel user={user} />}

                {activeTab === 'anggota' && (
                    <div className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A]">
                        <div className="flex justify-between items-center mb-8">
                            <h1 className="text-2xl font-bold font-montserrat">Data Anggota</h1>
                            <div className="flex gap-3 flex-1 justify-end">
                                <input 
                                    type="text" placeholder="Cari nama..." 
                                    className="p-3 border rounded-xl text-sm outline-none w-2/3 focus:ring-2 focus:ring-[#265F9C] shadow-sm"
                                    value={searchAnggota} onChange={(e) => {setSearchAnggota(e.target.value); setAnggotaPage(1);}}
                                />
                                <select className="p-3 border rounded-xl text-sm bg-gray-50 font-medium" value={filterRole} onChange={(e) => {setFilterRole(e.target.value); setAnggotaPage(1);}}>
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
                                    <tr key={i} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                        <td className="p-4 text-center text-[#7D7D7E]">{(anggotaPage - 1) * 10 + i + 1}</td>
                                        <td className="p-4 font-mono text-xs font-medium">{a.identitas}</td>
                                        <td className="p-4 font-bold">{a.nama}</td>
                                        <td className="p-4 text-center">
                                            <span className={`px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase border ${a.role === 'Karyawan' ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-blue-50 text-[#265F9C] border-blue-100'}`}>{a.role}</span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                            <button disabled={anggotaPage === 1} onClick={() => setAnggotaPage(anggotaPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Prev</button>
                            <span className="text-xs font-bold text-[#585858] uppercase">Halaman {anggotaPage} / {anggotaPagination.last_page || 1}</span>
                            <button disabled={anggotaPage === anggotaPagination.last_page} onClick={() => setAnggotaPage(anggotaPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Next</button>
                        </div>
                    </div>
                )}

                {activeTab === 'laporan' && (
                    <div className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A]">
                        <div className="flex justify-between items-center mb-8 print:hidden">
                            <h1 className="text-2xl font-bold font-montserrat">Laporan Praktik Kerja Lapangan (PKL)</h1>
                            <div className="flex gap-3">
                                <input type="text" placeholder="Penulis..." className="p-3 border rounded-xl text-sm w-64 outline-none focus:ring-2 focus:ring-[#265F9C] shadow-sm" value={filterPenulis} onChange={(e) => setFilterPenulis(e.target.value)} />
                                <input type="number" placeholder="Tahun..." className="p-3 border rounded-xl text-sm w-32 outline-none focus:ring-2 focus:ring-[#265F9C] shadow-sm" value={filterTahun} onChange={(e) => setFilterTahun(e.target.value)} />
                            </div>
                        </div>
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                                <tr>
                                    <th className="p-4 w-16 text-center">No</th>
                                    <th className="p-4">Judul Laporan PKL</th>
                                    <th className="p-4">Penulis (Siswa)</th>
                                    <th className="p-4 text-center">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                {dataLaporan.map((l, i) => (
                                    <tr key={i} className="border-b text-sm hover:bg-gray-50 transition-colors">
                                        <td className="p-4 text-center text-[#7D7D7E]">{(laporanPage-1)*10+i+1}</td>
                                        <td className="p-4 font-bold text-[#265F9C] leading-relaxed">{l.judul_koleksi}</td>
                                        <td className="p-4 font-medium">{l.nama_siswa_tetap}</td>
                                        <td className="p-4 text-center font-mono text-[#585858]">{l.tahun}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl print:hidden">
                            <button disabled={laporanPage === 1} onClick={() => setLaporanPage(laporanPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Prev</button>
                            <span className="text-xs font-bold text-[#585858] uppercase">Halaman {laporanPage} / {laporanPagination.last_page || 1}</span>
                            <button disabled={laporanPage === laporanPagination.last_page} onClick={() => setLaporanPage(laporanPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Next</button>
                        </div>
                        <div className="mt-8 flex justify-end print:hidden">
                            <button onClick={() => window.print()} className="px-8 py-3 bg-[#2E7D32] text-white rounded-xl text-xs font-bold hover:bg-[#1b5e20] shadow-md">Cetak Laporan</button>
                        </div>
                    </div>
                )}

                {activeTab === 'pengembalian' && <PengembalianPanel user={user} />}
                {activeTab === 'peminjaman' && <PeminjamanPanel user={user} />}
                {activeTab === 'riwayat_pinjam' && <RiwayatPinjamPanel user={user} />}

            </main>
        </div>
    );
};

export default AdminPanel;
