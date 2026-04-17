import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';
const EXPORT_URL = 'http://localhost:8000/laporan/kategori-populer/pdf'; // Sesuaikan route web.php Anda

const KategoriPopulerPanel = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                const res = await axios.get(`${API_BASE_URL}/laporan/kategori-populer`);
                setData(res.data);
            } catch (err) {
                console.error("Gagal ambil data kategori populer:", err);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);

    return (
        <div className="w-full animate-fadeIn">
            <div className="mb-6 flex justify-between items-end">
                <div>
                    <h2 className="text-2xl font-bold font-montserrat text-[#1A1A1A]">Kategori Terpopuler</h2>
                    <p className="text-sm text-[#7D7D7E]">Kategori buku yang paling diminati tahun {new Date().getFullYear()}.</p>
                </div>

                <button 
                    onClick={() => window.open(EXPORT_URL, '_blank')}
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
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858] text-center w-20">No</th>
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858]">Deskripsi Kategori</th>
                            <th className="p-4 text-[10px] font-bold uppercase tracking-widest text-[#585858] text-center">Total Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan="3" className="p-10 text-center animate-pulse text-gray-400">Menyinkronkan data...</td></tr>
                        ) : data.length > 0 ? (
                            data.map((item, index) => (
                                <tr key={index} className="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                                    <td className="p-4 text-center font-bold text-gray-400">
                                        {index + 1}
                                    </td>
                                    <td className="p-4">
                                        <p className="text-sm font-bold text-[#1A1A1A] uppercase">{item.deskripsi}</p>
                                    </td>
                                    <td className="p-4 text-center">
                                        <span className="bg-purple-50 text-purple-700 px-3 py-1 rounded-lg text-xs font-bold border border-purple-100">
                                            {item.total_dipinjam} Kali
                                        </span>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan="3" className="p-10 text-center text-gray-400">Belum ada data peminjaman kategori.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default KategoriPopulerPanel;