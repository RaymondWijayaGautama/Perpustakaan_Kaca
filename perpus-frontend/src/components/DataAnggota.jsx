import React, { useState, useEffect } from 'react';
import axios from 'axios';

const DataAnggota = () => {
    const [dataAnggota, setDataAnggota] = useState([]);
    const [loading, setLoading] = useState(false);
    const [anggotaPage, setAnggotaPage] = useState(1);
    const [anggotaPagination, setAnggotaPagination] = useState({});
    
    // State Filter
    const [searchName, setSearchName] = useState('');
    const [selectedRole, setSelectedRole] = useState('Semua');

    // Modal State
    const [showRiwayatModal, setShowRiwayatModal] = useState(false);
    const [riwayatData, setRiwayatData] = useState([]);
    const [selectedAnggota, setSelectedAnggota] = useState(null);
    const [loadingRiwayat, setLoadingRiwayat] = useState(false);

    // Fungsi Fetch dengan parameter lengkap ke Laravel
    const fetchAnggota = async () => {
        setLoading(true);
        try {
            const res = await axios.get(`http://localhost:8000/api/anggota`, {
                params: { 
                    page: anggotaPage,
                    search: searchName, 
                    role: selectedRole, 
                    per_page: 10
                }
            });
            // Tambahkan pengaman || [] agar tidak crash jika data kosong
            setDataAnggota(res.data.data || []);
            setAnggotaPagination(res.data);
        } catch (error) { 
            console.error("Gagal sinkronisasi data:", error); 
        } finally { 
            setLoading(false); 
        }
    };

    // Trigger fetch saat halaman, pencarian, atau role berubah
    useEffect(() => {
        const handler = setTimeout(() => {
            fetchAnggota();
        }, 400); 

        return () => clearTimeout(handler);
    }, [anggotaPage, searchName, selectedRole]);

    const handleShowRiwayat = async (anggota) => {
        if (!anggota.id_internal) return alert("ID tidak ditemukan!");
        setSelectedAnggota(anggota);
        setShowRiwayatModal(true);
        setLoadingRiwayat(true);
        try {
            const res = await axios.get(`http://localhost:8000/api/anggota/${anggota.id_internal}/riwayat`, {
                params: { role: anggota.role }
            });
            setRiwayatData(res.data || []);
        } catch (err) {
            setRiwayatData([]);
        } finally { setLoadingRiwayat(false); }
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 relative">
            {loading && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl">
                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <h1 className="text-2xl font-bold font-montserrat text-[#1A1A1A]">Data Anggota</h1>
                
                {/* Search & Role Gathers */}
                <div className="flex flex-1 w-full md:max-w-md gap-2">
                    <div className="relative flex-1">
                        <input 
                            type="text"
                            placeholder="Cari nama atau NISN/NIP..."
                            className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-[#265F9C] outline-none transition-all shadow-sm"
                            value={searchName}
                            onChange={(e) => {
                                setSearchName(e.target.value);
                                setAnggotaPage(1); 
                            }}
                        />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    
                    <select 
                        className="px-4 py-2.5 border border-gray-200 rounded-2xl text-[11px] font-black text-gray-500 bg-gray-50 outline-none focus:ring-2 focus:ring-[#265F9C] cursor-pointer shadow-sm uppercase tracking-tighter"
                        value={selectedRole}
                        onChange={(e) => {
                            setSelectedRole(e.target.value);
                            setAnggotaPage(1); 
                        }}
                    >
                        <option value="Semua">Semua Role</option>
                        <option value="Siswa">Siswa</option>
                        <option value="Karyawan">Karyawan</option>
                    </select>
                </div>
            </div>
            
            <table className="w-full text-left">
                <thead className="bg-gray-50 border-b uppercase text-[10px] font-bold text-gray-400">
                    <tr>
                        <th className="p-4">Identitas</th>
                        <th className="p-4">Nama Lengkap</th>
                        <th className="p-4 text-center">Role</th>
                        <th className="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                    {dataAnggota.length > 0 ? dataAnggota.map((a, i) => (
                        <tr key={i} className="hover:bg-gray-50/80 transition-colors group">
                            <td className="p-4 font-mono text-xs text-gray-600">{a.identitas}</td>
                            <td className="p-4 font-bold text-gray-800">{a.nama}</td>
                            <td className="p-4 text-center">
                                <span className={`px-2 py-1 rounded text-[9px] font-black uppercase ${
                                    a.role === 'Siswa' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'
                                }`}>
                                    {a.role}
                                </span>
                            </td>
                            <td className="p-4 text-center">
                                <button 
                                    onClick={() => handleShowRiwayat(a)}
                                    className="text-[#265F9C] hover:text-blue-800 font-bold text-xs uppercase tracking-tighter border-b-2 border-transparent hover:border-[#265F9C] transition-all"
                                > 
                                    Riwayat 
                                </button>
                            </td>
                        </tr>
                    )) : (
                        <tr>
                            <td colSpan="4" className="p-20 text-center text-gray-400 italic">
                                Data "{searchName}" tidak ditemukan pada role {selectedRole}.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>

            {/* Pagination UI */}
            <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                <button 
                    disabled={anggotaPage === 1} 
                    onClick={() => setAnggotaPage(anggotaPage - 1)} 
                    className="px-6 py-2 bg-white border rounded-lg disabled:opacity-30 text-[10px] font-black uppercase tracking-widest shadow-sm hover:shadow-md transition-all active:scale-95"
                > Prev </button>
                <div className="text-center">
                    <p className="text-[10px] font-black text-gray-400 uppercase mb-1">Halaman</p>
                    <p className="text-sm font-bold text-[#265F9C]">{anggotaPage} / {anggotaPagination.last_page || 1}</p>
                </div>
                <button 
                    disabled={anggotaPage >= (anggotaPagination.last_page || 1)} 
                    onClick={() => setAnggotaPage(anggotaPage + 1)} 
                    className="px-6 py-2 bg-white border rounded-lg disabled:opacity-30 text-[10px] font-black uppercase tracking-widest shadow-sm hover:shadow-md transition-all active:scale-95"
                > Next </button>
            </div>

            {/* Modal Riwayat */}
            {showRiwayatModal && (
                <div className="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl p-8 w-full max-w-4xl max-h-[85vh] flex flex-col relative shadow-2xl">
                        <button onClick={() => setShowRiwayatModal(false)} className="absolute top-6 right-6 text-gray-300 hover:text-red-500 text-3xl">&times;</button>
                        <h2 className="text-2xl font-bold text-[#1A1A1A] font-montserrat">Riwayat Pengembalian</h2>
                        <p className="text-sm text-gray-500 mb-6 uppercase tracking-widest font-semibold">{selectedAnggota?.nama}</p>
                        <div className="flex-1 overflow-y-auto border border-gray-100 rounded-2xl bg-gray-50/30">
                            {loadingRiwayat ? (
                                <div className="p-20 text-center">
                                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400">Menghubungkan Server...</p>
                                </div>
                            ) : (
                                <table className="w-full text-left">
                                    <thead className="bg-white sticky top-0 uppercase text-[9px] font-black text-gray-400 border-b">
                                        <tr>
                                            <th className="p-4">Koleksi Buku</th>
                                            <th className="p-4 text-center">Pinjam</th>
                                            <th className="p-4 text-center">Kembali</th>
                                            <th className="p-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 bg-white">
                                        {riwayatData.length > 0 ? riwayatData.map((h, i) => (
                                            <tr key={i} className="hover:bg-blue-50/30 transition-colors">
                                                <td className="p-4">
                                                    <p className="font-bold text-gray-800 text-sm leading-tight">{h.judul_koleksi}</p>
                                                    <p className="text-[10px] text-gray-400 font-mono mt-1 uppercase tracking-tighter">{h.ISBN}</p>
                                                </td>
                                                <td className="p-4 text-center font-mono text-[11px] text-gray-500">{h.tgl_peminjaman}</td>
                                                <td className="p-4 text-center font-mono text-[11px] text-green-600 font-bold">{h.tgl_kembali}</td>
                                                <td className="p-4 text-center">
                                                    <span className="bg-green-50 text-green-700 border border-green-100 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter">
                                                        {h.status_peminjaman}
                                                    </span>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr>
                                                <td colSpan="4" className="p-20 text-center text-gray-400 italic">Belum ada riwayat transaksi.</td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            )}
                        </div>
                        <div className="mt-6 flex justify-end">
                            <button onClick={() => setShowRiwayatModal(false)} className="px-10 py-3 bg-[#1A1A1A] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg active:scale-95 transition-all">Tutup</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default DataAnggota;