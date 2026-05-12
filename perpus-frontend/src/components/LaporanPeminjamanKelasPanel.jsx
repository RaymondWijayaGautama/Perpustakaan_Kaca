import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    BarChart, Bar, XAxis, YAxis, CartesianGrid, 
    Tooltip, ResponsiveContainer, Cell
} from 'recharts';

const API_BASE_URL = 'http://localhost:8000/api'; 

const LaporanPeminjamanKelasPanel = () => {
    const [chartData, setChartData] = useState([]);
    const [loading, setLoading] = useState(false);
    
    const currentDate = new Date();
    const [filterTahun, setFilterTahun] = useState(currentDate.getFullYear());
    const [filterBulan, setFilterBulan] = useState(''); // Kosong = Semua Bulan dalam Setahun

    const loadStatistik = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`${API_BASE_URL}/laporan/statistik-peminjaman-kelas`, {
                params: { 
                    tahun: filterTahun,
                    bulan: filterBulan !== '' ? filterBulan : null
                }
            });
            setChartData(response.data.data || []);
        } catch (error) {
            console.error('Gagal mengambil statistik kelas:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadStatistik();
    }, [filterTahun, filterBulan]);

    const handleExportPDF = () => {
        let exportUrl = `${API_BASE_URL}/laporan/export-pdf-peminjaman-kelas?tahun=${filterTahun}`;
        if (filterBulan !== '') exportUrl += `&bulan=${filterBulan}`;
        window.open(exportUrl, '_blank');
    };

    const daftarTahun = Array.from({ length: 5 }, (_, i) => currentDate.getFullYear() - i);
    const daftarBulan = [
        { id: '', nama: 'Semua Bulan' },
        { id: 1, nama: 'Januari' }, { id: 2, nama: 'Februari' },
        { id: 3, nama: 'Maret' }, { id: 4, nama: 'April' },
        { id: 5, nama: 'Mei' }, { id: 6, nama: 'Juni' },
        { id: 7, nama: 'Juli' }, { id: 8, nama: 'Agustus' },
        { id: 9, nama: 'September' }, { id: 10, nama: 'Oktober' },
        { id: 11, nama: 'November' }, { id: 12, nama: 'Desember' },
    ];

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4 border-b pb-4">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat text-[#1A1A1A]">
                        Peminjaman Berdasarkan Kelas
                    </h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Menampilkan perbandingan keaktifan peminjaman buku antar kelas.
                    </p>
                </div>

                <div className="flex items-center gap-3">
                    <button 
                        onClick={handleExportPDF}
                        className="flex items-center gap-2 px-4 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all"
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 2l4 4h-4V4z" />
                        </svg>
                        Export PDF
                    </button>

                    <div className="flex bg-gray-50 p-2 rounded-xl border gap-2">
                        <select 
                            value={filterBulan} 
                            onChange={(e) => setFilterBulan(e.target.value)}
                            className="p-2 bg-white border rounded-lg text-sm font-bold outline-none focus:ring-2 focus:ring-[#265F9C] text-[#265F9C] cursor-pointer"
                        >
                            {daftarBulan.map(b => (
                                <option key={b.id} value={b.id}>{b.nama}</option>
                            ))}
                        </select>
                        <select 
                            value={filterTahun} 
                            onChange={(e) => setFilterTahun(Number(e.target.value))}
                            className="p-2 bg-white border rounded-lg text-sm font-bold outline-none focus:ring-2 focus:ring-[#265F9C] text-[#265F9C] cursor-pointer"
                        >
                            {daftarTahun.map(t => (
                                <option key={t} value={t}>Tahun {t}</option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-[400px] text-[#585858] animate-pulse font-medium">
                    Memuat grafik statistik kelas...
                </div>
            ) : chartData.length === 0 ? (
                <div className="flex justify-center items-center h-[400px] text-gray-400 italic">
                    Belum ada data peminjaman di periode ini.
                </div>
            ) : (
                <div className="h-[400px] w-full mt-4">
                    <ResponsiveContainer width="100%" height="100%">
                        <BarChart
                            data={chartData}
                            margin={{ top: 20, right: 20, left: -20, bottom: 20 }}
                        >
                            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                            <XAxis 
                                dataKey="kelas" 
                                tickLine={false}
                                axisLine={false}
                                tick={{ fill: '#4B5563', fontSize: 11, fontWeight: 600 }}
                                dy={10}
                                angle={-15} // Dimiringkan sedikit teks kelasnya biar gak nabrak kalau panjang
                                textAnchor="end"
                            />
                            <YAxis 
                                tickLine={false}
                                axisLine={false}
                                tick={{ fill: '#9CA3AF', fontSize: 12 }}
                                allowDecimals={false}
                            />
                            <Tooltip 
                                cursor={{fill: '#f3f4f6'}}
                                contentStyle={{ borderRadius: '12px', border: '1px solid #e5e7eb', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                                labelStyle={{ fontWeight: 'bold', color: '#1A1A1A', marginBottom: '4px' }}
                                formatter={(value) => [`${value} Peminjaman`, 'Total Buku']}
                                labelFormatter={(label) => `Kelas: ${label}`}
                            />
                            <Bar 
                                dataKey="total" 
                                radius={[6, 6, 0, 0]} // Melengkungkan sudut atas batang
                                maxBarSize={60}
                            >
                                {/* Looping warna biru agar menarik */}
                                {chartData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={index % 2 === 0 ? '#265F9C' : '#4f85be'} />
                                ))}
                            </Bar>
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            )}
        </div>
    );
};

export default LaporanPeminjamanKelasPanel;