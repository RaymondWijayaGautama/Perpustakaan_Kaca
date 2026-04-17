import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const DaftarPeminjaman = () => {
    const [data, setData] = useState([]);
    const [filterStatus, setFilterStatus] = useState('Semua');
    const [loading, setLoading] = useState(false);

    const fetchData = useCallback(async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/peminjaman', {
                params: { status: filterStatus }
            });
            setData(res.data);
        } catch (error) {
            console.error("Gagal ambil data pinjaman:", error);
        } finally {
            setLoading(false);
        }
    }, [filterStatus]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);
    const handleKembalikan = async (idPeminjaman, judulBuku, namaPeminjam) => {
        const yakin = window.confirm(`Terima pengembalian buku "${judulBuku}" dari ${namaPeminjam}?`);
        if (!yakin) return;

        try {
            setLoading(true);
            await axios.post(`http://localhost:8000/api/pengembalian/proses/${idPeminjaman}`, {
                kondisi_buku: 'Baik',
                denda: 0
            });
            alert('Mantap! Buku berhasil dikembalikan ke perpustakaan.');
            fetchData(); 
        } catch (error) {
            alert(error.response?.data?.message || "Gagal memproses pengembalian.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white rounded-2xl shadow-sm p-8 max-w-6xl mx-auto border border-gray-100 text-[#1A1A1A] relative">
            {loading && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
                    <div className="w-10 h-10 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat text-[#265F9C]">Sirkulasi Perpustakaan</h1>
                    <p className="text-sm text-gray-500">Pantau dan kelola peminjaman buku aktif</p>
                </div>
                
                <select 
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-bold outline-none focus:ring-2 focus:ring-[#265F9C]"
                    value={filterStatus}
                    onChange={(e) => setFilterStatus(e.target.value)}
                >
                    <option value="Semua">Semua Riwayat</option>
                    <option value="Dipinjam">Sedang Dipinjam</option>
                    <option value="Kembali">Sudah Kembali</option>
                </select>
            </div>

            <div className="overflow-x-auto rounded-xl border border-gray-100">
                <table className="w-full text-left border-collapse">
                    <thead className="bg-gray-50 uppercase text-[10px] font-black text-[#585858] border-b">
                        <tr>
                            <th className="p-4 whitespace-nowrap">Peminjam</th>
                            <th className="p-4 min-w-[200px]">Judul Buku</th>
                            <th className="p-4 whitespace-nowrap">Batas Kembali</th>
                            <th className="p-4 text-center">Status</th>

                            <th className="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {data.length > 0 ? (
                            data.map((item, i) => (
                                <tr key={item.id_peminjaman || i} className="text-sm hover:bg-blue-50/30 transition-colors">
                                    <td className="p-4 font-bold text-gray-800">{item.nama_peminjam}</td>
                                    <td className="p-4 text-[#265F9C] font-medium leading-relaxed">{item.judul_buku}</td>
                                    <td className="p-4 font-mono text-xs text-red-600 font-semibold">{item.tgl_harus_kembali?.substring(0,10)}</td>
                                    <td className="p-4 text-center">
                                        <span className={`px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider ${
                                            item.status_peminjaman === 'Dipinjam' 
                                            ? 'bg-orange-50 text-orange-600 border border-orange-200' 
                                            : 'bg-green-50 text-green-600 border border-green-200'
                                        }`}>
                                            {item.status_peminjaman}
                                        </span>
                                    </td>
                                    <td className="p-4 text-center">
                                        {item.status_peminjaman === 'Dipinjam' ? (
                                            <button 
                                                onClick={() => handleKembalikan(item.id_peminjaman, item.judul_buku, item.nama_peminjam)}
                                                className="bg-[#265F9C] hover:bg-blue-800 text-white text-[10px] font-bold px-4 py-2 rounded-lg transition-all active:scale-95 shadow-sm"
                                            >
                                                TERIMA BUKU
                                            </button>
                                        ) : (
                                            <span className="text-[10px] font-bold text-gray-400">SELESAI</span>
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="5" className="text-center py-12 text-gray-400 italic text-sm">
                                    Tidak ada data sirkulasi yang ditemukan.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default DaftarPeminjaman;