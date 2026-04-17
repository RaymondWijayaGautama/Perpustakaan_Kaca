import React, { useState, useEffect } from 'react';
import axios from 'axios';

const LaporanSiswaTerajinPanel = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const loadData = async () => {
            setLoading(true);
            try {
                // Pakai URL langsung untuk tes
                const res = await axios.get('http://127.0.0.1:8000/api/laporan/siswa-terajin');
                console.log("DATA BERHASIL MASUK:", res.data);
                setData(res.data);
            } catch (err) {
                console.error("ERROR KONEKSI:", err);
                alert("Gagal koneksi ke Backend! Cek apakah Laravel sudah running.");
            } finally {
                setLoading(false);
            }
        };
        loadData();
    }, []);

    return (
        <div className="w-full">
            <div className="mb-6 flex justify-between items-center">
                <h2 className="text-2xl font-bold">Peringkat Siswa Terajin</h2>
                <button 
                    onClick={() => window.open('http://127.0.0.1:8000/laporan/siswa-terajin/pdf', '_blank')}
                    className="px-4 py-2 bg-red-600 text-white rounded-lg font-bold"
                >
                    EKSPOR PDF
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 text-[10px] uppercase font-bold text-gray-500">
                        <tr>
                            <th className="p-4 text-center">Rank</th>
                            <th className="p-4">Nama Lengkap</th>
                            <th className="p-4 text-center">Total Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr><td colSpan="3" className="p-10 text-center">Loading...</td></tr>
                        ) : data.length > 0 ? (
                            data.map((item, index) => (
                                <tr key={index} className="border-t border-gray-100">
                                    <td className="p-4 text-center font-bold text-gray-400">#{index + 1}</td>
                                    <td className="p-4 font-bold text-gray-800">{item.nama_siswa_tetap}</td>
                                    <td className="p-4 text-center">
                                        <span className="bg-blue-50 text-blue-700 px-3 py-1 rounded-md text-xs font-bold">
                                            {item.peminjaman_count} Peminjaman
                                        </span>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan="3" className="p-10 text-center text-gray-400">Data masih kosong di API.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default LaporanSiswaTerajinPanel;