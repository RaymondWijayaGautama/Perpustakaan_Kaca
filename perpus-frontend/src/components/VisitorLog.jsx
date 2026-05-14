import React, { useEffect, useState } from 'react';
import axios from 'axios';

const VisitorLog = () => {
    const [visitors, setVisitors] = useState([]);
    const [loading, setLoading] = useState(true);

    // Fungsi untuk ambil data dari API yang barusan kita buat
    const getVisitors = async () => {
    setLoading(true);
    try {
        // 1. Pastikan URL-nya benar (pakai http://localhost:8000 jika beda port)
        const response = await axios.get('http://localhost:8000/api/log/visitors');
        
        console.log("Data API:", response.data); // Ini buat lo cek di Console (F12)

        // 2. Laravel Paginate membungkus data di dalam objek 'data' lagi
        // Struktur: response (axios) -> data (laravel) -> data (array isi log)
        if (response.data && response.data.data) {
            setVisitors(response.data.data); 
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

    if (loading) return <div className="p-5 text-center">Sabar ya, lagi narik data...</div>;

    return (
        <div className="p-8 bg-white rounded-lg shadow-md">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Log Pengunjung Perpustakaan</h1>
            
            <div className="overflow-x-auto">
                <table className="min-w-full table-auto border-collapse border border-gray-200">
                    <thead>
                        <tr className="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                            <th className="p-4 border-b">Nama Pengunjung</th>
                            <th className="p-4 border-b">NPM</th>
                            <th className="p-4 border-b">Tujuan</th>
                            <th className="p-4 border-b">Waktu Masuk</th>
                        </tr>
                    </thead>
                    <tbody className="text-sm text-gray-600">
                        {visitors.length > 0 ? (
                            visitors.map((item) => (
                                <tr key={item.id} className="hover:bg-gray-50 transition">
                                    <td className="p-4 border-b">{item.visitor_name}</td>
                                    <td className="p-4 border-b">{item.visitor_npm}</td>
                                    <td className="p-4 border-b">
                                        <span className="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                                            {item.purpose || 'Kunjungan Umum'}
                                        </span>
                                    </td>
                                    <td className="p-4 border-b">
                                        {new Date(item.date).toLocaleString('id-ID')}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="4" className="p-4 text-center text-gray-500">
                                    Belum ada data pengunjung hari ini.
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