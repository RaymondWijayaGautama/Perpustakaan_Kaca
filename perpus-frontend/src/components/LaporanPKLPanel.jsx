import React, { useState, useEffect } from 'react';
import axios from 'axios';

const LaporanPKLPanel = () => {
    const [dataLaporan, setDataLaporan] = useState([]);
    const [laporanPage, setLaporanPage] = useState(1);
    const [laporanPagination, setLaporanPagination] = useState({});
    
    // State terpisah untuk Search dan Filter
    const [searchJudul, setSearchJudul] = useState('');
    const [filterPenulis, setFilterPenulis] = useState('');
    const [filterTahun, setFilterTahun] = useState('');
    
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const fetchLaporan = async () => {
            setLoading(true);
            try {
                // Pastikan endpoint mengarah ke /api/laporan-pkl
                const res = await axios.get('http://localhost:8000/api/laporan-pkl', {
                    params: { 
                        page: laporanPage, 
                        judul: searchJudul,     // Parameter baru untuk Judul
                        penulis: filterPenulis, // Parameter untuk Penulis
                        tahun: filterTahun,     // Parameter untuk Tahun
                        per_page: 10 
                    }
                });
                setDataLaporan(res.data.data || []);
                setLaporanPagination(res.data);
            } catch (error) { 
                console.error("Gagal memuat laporan:", error); 
            } finally { 
                setLoading(false); 
            }
        };

        const handler = setTimeout(() => {
            fetchLaporan();
        }, 400);

        return () => clearTimeout(handler);
    }, [laporanPage, searchJudul, filterPenulis, filterTahun]);

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A] relative">
            {loading && (
                <div className="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl">
                    <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8 print:hidden">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Laporan PKL</h1>
                    <p className="text-xs text-gray-500 mt-1">Arsip dan Referensi Laporan Siswa</p>
                </div>
                
                {/* Area Input Pencarian & Filter */}
                <div className="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    {/* Search Judul Utama */}
                    <div className="relative flex-1 xl:w-64">
                        <input 
                            type="text" 
                            placeholder="Cari Judul Laporan..." 
                            className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#265F9C] shadow-sm transition-all" 
                            value={searchJudul} 
                            onChange={(e) => {
                                setSearchJudul(e.target.value);
                                setLaporanPage(1); 
                            }} 
                        />
                        <div className="absolute left-3 top-3 text-gray-400">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    {/* Filter Penulis */}
                    <div className="relative flex-1 xl:w-48">
                        <input 
                            type="text" 
                            placeholder="Filter Penulis..." 
                            className="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32] shadow-sm transition-all bg-gray-50 focus:bg-white" 
                            value={filterPenulis} 
                            onChange={(e) => {
                                setFilterPenulis(e.target.value);
                                setLaporanPage(1); 
                            }} 
                        />
                        <div className="absolute left-3 top-3 text-[#2E7D32]">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>

                    {/* Filter Tahun */}
                    <input 
                        type="number" 
                        placeholder="Tahun" 
                        className="w-full md:w-28 px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32] shadow-sm transition-all bg-gray-50 focus:bg-white" 
                        value={filterTahun} 
                        onChange={(e) => {
                            setFilterTahun(e.target.value);
                            setLaporanPage(1); 
                        }} 
                    />
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b border-gray-200">
                        <tr>
                            <th className="p-4 w-16 text-center">No</th>
                            <th className="p-4 w-1/2">Judul Laporan PKL</th>
                            <th className="p-4">Penulis (Siswa)</th>
                            <th className="p-4 text-center">Tahun</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {dataLaporan.length > 0 ? dataLaporan.map((l, i) => (
                            <tr key={i} className="text-sm hover:bg-blue-50/30 transition-colors">
                                <td className="p-4 text-center text-[#7D7D7E] font-mono">{(laporanPage - 1) * 10 + i + 1}</td>
                                <td className="p-4 font-bold text-[#265F9C] leading-snug">{l.judul_koleksi}</td>
                                <td className="p-4">
                                    <p className="font-semibold text-gray-800">{l.nama_siswa_tetap}</p>
                                    {l.nisn_siswa && <p className="text-[10px] text-gray-400 font-mono mt-0.5">{l.nisn_siswa}</p>}
                                </td>
                                <td className="p-4 text-center font-mono font-bold text-[#585858] bg-gray-50/50">{l.tahun}</td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="4" className="p-16 text-center">
                                    <svg className="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p className="text-gray-400 italic font-montserrat text-sm">Laporan tidak ditemukan.</p>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 print:hidden">
                <button 
                    disabled={laporanPage === 1} 
                    onClick={() => setLaporanPage(laporanPage - 1)} 
                    className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm transition-all hover:bg-gray-100 active:scale-95"
                > Prev </button>
                <span className="text-xs font-bold text-[#585858] uppercase tracking-widest">
                    Halaman <span className="text-[#265F9C]">{laporanPage}</span> / {laporanPagination.last_page || 1}
                </span>
                <button 
                    disabled={laporanPage >= (laporanPagination.last_page || 1)} 
                    onClick={() => setLaporanPage(laporanPage + 1)} 
                    className="px-5 py-2 bg-white border border-gray-200 rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm transition-all hover:bg-gray-100 active:scale-95"
                > Next </button>
            </div>

            <div className="mt-6 flex justify-end print:hidden">
                <button 
                    onClick={() => window.print()} 
                    className="px-8 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white rounded-xl text-xs font-bold shadow-md transition-all active:scale-95 flex items-center gap-2"
                > 
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan 
                </button>
            </div>
        </div>
    );
};

export default LaporanPKLPanel;