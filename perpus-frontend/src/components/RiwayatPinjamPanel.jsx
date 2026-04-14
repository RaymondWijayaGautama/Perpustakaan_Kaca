import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

const RiwayatPinjamPanel = () => {
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

    const handleUpdate = async (id, statusLama) => {
        const statusBaru = statusLama === 'Dipinjam' ? 'Kembali' : 'Dipinjam';
        const confirmMsg = `Ubah status transaksi ID #${id} menjadi "${statusBaru}"?`;

        if (window.confirm(confirmMsg)) {
            try {
                setLoading(true);
                await axios.put(`http://localhost:8000/api/peminjaman/${id}`, {
                    status_peminjaman: statusBaru,
                    kondisi_buku: 'Baik' 
                });
                alert("Data berhasil diupdate!");
                fetchData(); 
            } catch (error) {
                alert("Gagal update data!");
                console.error(error);
            } finally {
                setLoading(false);
            }
        }
    };

    const handleDelete = async (id) => {
        if (window.confirm("Apakah anda yakin ingin menghapus (mengarsipkan) data ini?")) {
            try {
                setLoading(true);
                await axios.delete(`http://localhost:8000/api/peminjaman/${id}`);
                alert("Data berhasil dihapus dari daftar aktif!");
                fetchData(); // Refresh tabel
            } catch (error) {
                alert("Gagal menghapus data!");
                console.error(error);
            } finally {
                setLoading(false);
            }
        }
    };

    return (
        <div className="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 text-[#1A1A1A] relative">
            {loading && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
                    <div className="w-10 h-10 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex justify-between items-center mb-8">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Daftar Peminjaman</h1>
                    <p className="text-sm text-gray-500">Data keluar masuk buku (Raymond Mode)</p>
                </div>
                
                <select 
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-bold outline-none focus:ring-2 focus:ring-[#265F9C]"
                    value={filterStatus}
                    onChange={(e) => setFilterStatus(e.target.value)}
                >
                    <option value="Semua">Semua Status</option>
                    <option value="Dipinjam">Sedang Dipinjam</option>
                    <option value="Kembali">Sudah Kembali</option>
                </select>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 uppercase text-[10px] font-black text-[#585858] border-b">
                        <tr>
                            <th className="p-4">Peminjam</th>
                            <th className="p-4">Judul Buku</th>
                            <th className="p-4">Tgl Pinjam</th>
                            <th className="p-4 text-center">Status</th>
                            <th className="p-4 text-center">Aksi</th> 
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {data.map((item, i) => (
                            <tr key={i} className="text-sm hover:bg-blue-50/30 transition-colors">
                                <td className="p-4 font-bold">{item.nama_peminjam}</td>
                                <td className="p-4 text-[#265F9C] font-medium">{item.judul_buku}</td>
                                <td className="p-4 font-mono text-xs">{item.tgl_peminjaman}</td>
                                <td className="p-4 text-center">
                                    <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase ${
                                        item.status_peminjaman === 'Dipinjam' 
                                        ? 'bg-orange-50 text-orange-600 border border-orange-100' 
                                        : 'bg-green-50 text-green-600 border border-green-100'
                                    }`}>
                                        {item.status_peminjaman}
                                    </span>
                                </td>
                                <td className="p-4 text-center">
                                    <button 
                                        onClick={() => handleUpdate(item.id_peminjaman, item.status_peminjaman)}
                                        className="bg-white border border-gray-200 hover:border-[#265F9C] hover:text-[#265F9C] px-3 py-1 rounded-lg text-[10px] font-bold transition-all"
                                    >
                                        EDIT
                                    </button>

                                    <button 
                                        onClick={() => handleDelete(item.id_peminjaman)}
                                        className="ml-2 bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white px-3 py-1 rounded-lg text-[10px] font-bold transition-all"
                                    >
                                        HAPUS
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {data.length === 0 && !loading && (
                    <div className="text-center py-10 text-gray-400 italic text-sm">Belum ada data transaksi.</div>
                )}
            </div>
        </div>
    );
};

export default RiwayatPinjamPanel;