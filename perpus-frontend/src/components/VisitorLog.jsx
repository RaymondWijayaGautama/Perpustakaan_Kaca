import React, { useEffect, useState } from 'react';
import axios from 'axios';

const VisitorLog = () => {
    const [visitors, setVisitors] = useState([]);
    const [loading, setLoading] = useState(true);

    const getVisitors = async () => {
        setLoading(true);
        try {
            const response = await axios.get('http://localhost:8000/api/log/visitors');
            if (response.data && response.data.data) {
                setVisitors(response.data.data); 
            } else if (Array.isArray(response.data)) {
                setVisitors(response.data);
            }
        } catch (error) {
            console.error("Gagal load data pengunjung:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getVisitors();
    }, []);

    // FUNGSI BARU: Buat maksa React baca data mentah MySQL sebagai UTC
    const formatWaktuWIB = (dateString) => {
        if (!dateString) return '-';
        // Cek kalau formatnya mentah "YYYY-MM-DD HH:MM:SS" (nggak ada huruf T)
        let safeDate = dateString;
        if (dateString.includes(' ') && !dateString.includes('T')) {
            // Ubah spasi jadi 'T' dan tambahin 'Z' biar React tau ini jam UTC (database default)
            safeDate = dateString.replace(' ', 'T') + 'Z';
        }
        return new Date(safeDate).toLocaleString('id-ID');
    };

    if (loading) return <div className="p-5 text-center">Sabar ya, lagi narik data...</div>;

    return (
        <div className="p-8 bg-white rounded-lg shadow-md">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Log Pengunjung Perpustakaan</h1>
            
            <div className="overflow-x-auto">
                <table className="min-w-full table-auto border-collapse border-gray-200">
                    <thead>
                        <tr className="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                            <th className="p-4 border-b">Nama Pengunjung</th>
                            <th className="p-4 border-b">ID / NIP</th>
                            <th className="p-4 border-b">Waktu Masuk</th>
                            <th className="p-4 border-b">Waktu Keluar</th>
                        </tr>
                    </thead>
                    <tbody className="text-sm text-gray-600">
                        {visitors.length > 0 ? (
                            visitors.map((item, index) => {
                                const identitas = item.ID_SISWA_TETAP || item.NIP_KARYAWAN || '-';
                                const nama = item.nama_pengunjung || `User ${identitas}`;

                                return (
                                    <tr key={item.ID_KUNJUNGAN || index} className="hover:bg-gray-50 transition">
                                        <td className="p-4 border-b font-medium">{nama}</td>
                                        <td className="p-4 border-b">{identitas}</td>
                                        <td className="p-4 border-b">
                                            {/* PANGGIL FUNGSI DI SINI */}
                                            {item.START_KUNJUNGAN ? formatWaktuWIB(item.START_KUNJUNGAN) : '-'}
                                        </td>
                                        <td className="p-4 border-b">
                                            {/* PANGGIL FUNGSI DI SINI */}
                                            {item.END_KUNJUNGAN ? (
                                                formatWaktuWIB(item.END_KUNJUNGAN)
                                            ) : (
                                                <span className="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Belum Check-out</span>
                                            )}
                                        </td>
                                    </tr>
                                );
                            })
                        ) : (
                            <tr>
                                <td colSpan="4" className="p-4 text-center text-gray-500">
                                    Belum ada data pengunjung.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default VisitorLog;