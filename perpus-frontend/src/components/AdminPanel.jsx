import React, { useState, useEffect } from 'react';
import axios from 'axios';
import ManajemenBukuPanel from './ManajemenBukuPanel';
import PengembalianPanel from './PengembalianPanel';
import PeminjamanPanel from './PeminjamanPanel';
import RiwayatPinjamPanel from './RiwayatPinjamPanel';
import PemusnahanPanel from './PemusnahanPanel';
import LaporanPeminjamanBulananPanel from './LaporanPeminjamanBulananPanel';
import DataAnggota from './DataAnggota'; 
import LaporanPKLPanel from './LaporanPKLPanel'; // Import komponen baru

const AdminPanel = ({ user, onLogout }) => {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [stats, setStats] = useState({ total_buku: 0, total_siswa: 0, total_laporan: 0 });
    const [loading, setLoading] = useState(false);

    // Semua state laporan (dataLaporan, dll) sudah kita hapus dari sini karena sudah pindah!

    useEffect(() => {
        const fetchStats = async () => {
            setLoading(true);
            try {
                const resStats = await axios.get('http://localhost:8000/api/dashboard/stats');
                setStats(resStats.data);
            } catch (error) { 
                console.error(error); 
            } finally {
                setLoading(false);
            }
        };
        
        // Hanya fetch stats jika sedang di dashboard
        if(activeTab === 'dashboard') {
            fetchStats();
        }
    }, [activeTab]);

    return (
        <div className="min-h-screen bg-[#F6F7F9] flex font-roboto text-[#1A1A1A]">
            <aside className="w-64 bg-[#265F9C] text-white p-6 flex flex-col shadow-xl print:hidden">
                <h2 className="font-montserrat font-bold text-xl mb-10 tracking-tight text-center uppercase">Kaca Admin</h2>
                <nav className="flex-1 space-y-2">
                    <div onClick={() => setActiveTab('dashboard')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'dashboard' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Dashboard</div>
                    <div onClick={() => setActiveTab('buku')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'buku' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Manajemen Buku</div>
                    <div onClick={() => setActiveTab('anggota')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'anggota' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Data Anggota</div>
                    <div onClick={() => setActiveTab('laporan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Laporan PKL</div>
                    <div onClick={() => setActiveTab('pengembalian')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'pengembalian' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Pengembalian</div>
                    <div onClick={() => setActiveTab('peminjaman')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'peminjaman' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Peminjaman Buku</div>
                    <div onClick={() => setActiveTab('pemusnahan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'pemusnahan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Pemusnahan Buku</div>
                    <div onClick={() => setActiveTab('riwayat_pinjam')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'riwayat_pinjam' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Riwayat Peminjaman</div>
                    <div onClick={() => setActiveTab('laporan_peminjaman_bulanan')} className={`p-3 rounded cursor-pointer transition-all ${activeTab === 'laporan_peminjaman_bulanan' ? 'bg-white/20 font-bold border-l-4 border-white' : 'hover:bg-white/10'}`}>Statistik Peminjaman</div>
                </nav>
                <button onClick={onLogout} className="mt-auto p-2 text-red-200 font-bold hover:text-white transition-colors text-left">Keluar Sistem</button>
            </aside>

            <main className="flex-1 p-10 overflow-y-auto print:p-0 relative">
                {/* Global Loading sekarang hanya fokus untuk Stats Dashboard */}
                {loading && activeTab === 'dashboard' && (
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
                {activeTab === 'anggota' && <DataAnggota />}
                {/* Panggil komponen LaporanPKLPanel yang sudah dipisah */}
                {activeTab === 'laporan' && <LaporanPKLPanel />} 
                {activeTab === 'pengembalian' && <PengembalianPanel user={user} />}
                {activeTab === 'peminjaman' && <PeminjamanPanel user={user} />}
                {activeTab === 'pemusnahan' && <PemusnahanPanel user={user} />}
                {activeTab === 'riwayat_pinjam' && <RiwayatPinjamPanel user={user} />}
                {activeTab === 'laporan_peminjaman_bulanan' && <LaporanPeminjamanBulananPanel />}
            </main>
        </div>
    );
};

export default AdminPanel;