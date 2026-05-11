import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    XAxis, YAxis, CartesianGrid, 
    Tooltip, ResponsiveContainer, Area, AreaChart 
} from 'recharts';

// Pastikan port ini sesuai dengan URL Laravel lu
const API_BASE_URL = 'http://localhost:8000/api'; 

const LaporanStatistikKunjungan = () => {
    const [chartData, setChartData] = useState([]);
    const [loading, setLoading] = useState(false);
    
    // Default filter: Tahun saat ini
    const currentDate = new Date();
    const [filterTahun, setFilterTahun] = useState(currentDate.getFullYear());

    // Fungsi Fetch API
    const loadStatistik = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`${API_BASE_URL}/laporan/statistik-kunjungan-bulanan`, {
                params: { tahun: filterTahun }
            });
            setChartData(response.data.data || []);
        } catch (error) {
            console.error('Gagal mengambil statistik:', error);
        } finally {
            setLoading(false);
        }
    };

    // Auto-fetch data saat komponen dimuat atau filterTahun berubah
    useEffect(() => {
        loadStatistik();
    }, [filterTahun]);

    // Opsi tahun: dari 4 tahun lalu sampai tahun sekarang
    const daftarTahun = Array.from({ length: 5 }, (_, i) => currentDate.getFullYear() - i);

    // Fungsi trigger export PDF
    const handleExportPDF = () => {
        window.open(`${API_BASE_URL}/laporan/export-pdf-statistik-kunjungan-bulanan?tahun=${filterTahun}`, '_blank');
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            {/* Header & Filter Div */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4 border-b pb-4">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat text-[#1A1A1A]">
                        Statistik Kunjungan Bulanan
                    </h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Memantau tren jumlah kunjungan perpustakaan sepanjang tahun {filterTahun}.
                    </p>
                </div>

                {/* Filter & Tombol Export */}
                <div className="flex items-center gap-3">
                    {/* Tombol Export PDF */}
                    <button 
                        onClick={handleExportPDF}
                        className="flex items-center gap-2 px-4 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all"
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 2l4 4h-4V4z" />
                        </svg>
                        Export PDF
                    </button>

                    {/* Dropdown Filter */}
                    <div className="flex bg-gray-50 p-2 rounded-xl border">
                        <select 
                            value={filterTahun} 
                            onChange={(e) => setFilterTahun(Number(e.target.value))}
                            className="p-2 bg-white border rounded-lg text-sm font-bold outline-none focus:ring-2 focus:ring-[#265F9C] text-[#265F9C] cursor-pointer shadow-sm"
                        >
                            {daftarTahun.map(t => (
                                <option key={t} value={t}>Tahun {t}</option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>

            {/* Container Chart */}
            {loading ? (
                <div className="flex justify-center items-center h-[400px] text-[#585858] animate-pulse font-medium">
                    Memuat grafik statistik...
                </div>
            ) : (
                <div className="h-[400px] w-full mt-4">
                    <ResponsiveContainer width="100%" height="100%">
                        <AreaChart
                            data={chartData}
                            margin={{ top: 20, right: 20, left: -20, bottom: 0 }}
                        >
                            {/* Gradasi Warna Biru untuk Chart */}
                            <defs>
                                <linearGradient id="colorBlue" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="#265F9C" stopOpacity={0.4}/>
                                    <stop offset="95%" stopColor="#265F9C" stopOpacity={0}/>
                                </linearGradient>
                            </defs>
                            
                            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f3f4f6" />
                            
                            <XAxis 
                                dataKey="bulan" 
                                tickLine={false}
                                axisLine={false}
                                tick={{ fill: '#9CA3AF', fontSize: 12, fontWeight: 600 }}
                                dy={10}
                            />
                            
                            <YAxis 
                                tickLine={false}
                                axisLine={false}
                                tick={{ fill: '#9CA3AF', fontSize: 12 }}
                                allowDecimals={false}
                            />
                            
                            <Tooltip 
                                contentStyle={{ borderRadius: '12px', border: '1px solid #e5e7eb', boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)' }}
                                labelStyle={{ fontWeight: 'bold', color: '#1A1A1A', marginBottom: '4px' }}
                                formatter={(value) => [`${value} Kunjungan`, 'Total']}
                                labelFormatter={(label) => `Bulan ${label} ${filterTahun}`}
                            />
                            
                            <Area 
                                type="monotone" 
                                dataKey="total" 
                                stroke="#265F9C" 
                                strokeWidth={3}
                                fillOpacity={1} 
                                fill="url(#colorBlue)" 
                                activeDot={{ r: 6, fill: '#fff', stroke: '#265F9C', strokeWidth: 3 }}
                            />
                        </AreaChart>
                    </ResponsiveContainer>
                </div>
            )}
        </div>
    );
};

export default LaporanStatistikKunjungan;