import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';
const EXPORT_URL = 'http://localhost:8000/laporan/buku-terpopuler/pdf'; // Sesuaikan route web.php kamu

const BukuTerpopulerPanel = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                const res = await axios.get(`${API_BASE_URL}/laporan/buku-terpopuler`);
                setData(res.data);
            } catch (err) {
                console.error("Gagal ambil data buku populer:", err);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);

    const handleExportPDF = () => {
        window.open(EXPORT_URL, '_blank');
    };

    return (
        <div className="w-full animate-fadeIn">
            <div className="mb-6 flex justify-between items-end">
                <div>
                    <h2 className="text-2xl font-bold font-montserrat text-[#1A1A1A]">Buku Terpopuler</h2>
                    <p className="text-sm text-[#7D7D7E]">Daftar buku yang paling sering dipinjam tahun {new Date().getFullYear()}.</p>
                </div>

                <button 
                    onClick={handleExportPDF}
                    className="px-6 py-2.5 bg-[#C62828] text-white rounded-xl text-xs font-bold hover:bg-[#b71c1c] shadow-md transition-all flex items-center gap-2 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    EKSPOR PDF
                </button>
            </div>

            <div className="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858] text-center w-20">Rank</th>
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858]">Informasi Buku</th>
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858] text-center">Total Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan="3" className="p-10 text-center animate-pulse text-gray-400">Memuat data buku...</td></tr>
                        ) : data.length > 0 ? (
                            data.map((item, index) => (
                                <tr key={index} className="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                                    <td className="p-4 text-center">
                                        <span className={`inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-xs ${index < 3 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500'}`}>
                                            {index + 1}
                                        </span>
                                    </td>
                                    <td className="p-4">
                                        <p className="text-sm font-bold text-[#1A1A1A] uppercase">{item.judul_koleksi}</p>
                                        <p className="text-[10px] text-[#7D7D7E]">ISBN: {item.ISBN} | Pengarang: {item.pengarang}</p>
                                    </td>
                                    <td className="p-4 text-center">
                                        <span className="bg-orange-50 text-orange-700 px-3 py-1 rounded-lg text-xs font-bold border border-orange-100">
                                            {item.total_dipinjam} Kali Dipinjam
                                        </span>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan="3" className="p-10 text-center text-gray-400">Belum ada data peminjaman buku.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default BukuTerpopulerPanel;