import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import useConfirmDialog from './useConfirmDialog';

const RiwayatPinjamPanel = ({ user }) => {
    const { confirm, ConfirmDialog } = useConfirmDialog();
    const [data, setData] = useState([]);
    const [filterStatus, setFilterStatus] = useState('Semua');
    const [loading, setLoading] = useState(false);

    const getUserNip = (user) => (
        user?.nip_karyawan ||
        user?.NIP_KARYAWAN ||
        user?.nip ||
        user?.NIP ||
        ''
    );

    const fetchData = useCallback(async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/peminjaman', {
                params: { status: filterStatus }
            });
            // Extend data with some frontend logic for perpanjangan eligibility
            const enhancedData = res.data.map(item => {
                const now = new Date();
                const tglHarusKembali = item.tgl_harus_kembali ? new Date(item.tgl_harus_kembali.replace(' ', 'T')) : null;
                const isOverdue = tglHarusKembali && now > tglHarusKembali;
                const extensionCount = item.jumlah_perpanjangan || 0;
                
                return {
                    ...item,
                    isOverdue,
                    canExtend: item.status_peminjaman === 'Dipinjam' && !isOverdue && extensionCount < 2
                };
            });
            setData(enhancedData);
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

        const approved = await confirm({
            title: 'Ubah Status',
            message: confirmMsg,
            confirmLabel: 'Ya, Ubah',
            tone: 'primary',
        });

        if (!approved) return;

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
    };

    const isPustakawan = (user) => {
        const jabatan = (user?.JABATAN_FUNGSIONAL || user?.jabatan_fungsional || '').toLowerCase();
        return jabatan === 'pustakawan';
    };

    const handlePerpanjang = async (id, judul) => {
        const approved = await confirm({
            title: 'Perpanjang Peminjaman',
            message: `Perpanjang masa pinjam buku "${judul}" selama 7 hari?`,
            confirmLabel: 'Ya, Perpanjang',
            tone: 'primary',
        });

        if (!approved) return;

        try {
            setLoading(true);
            const response = await axios.post(`http://localhost:8000/api/peminjaman/perpanjang/${id}`, {
                editor_nip_karyawan: getUserNip(user)
            });
            alert(response.data.message);
            fetchData();
        } catch (error) {
            alert(error.response?.data?.message || "Gagal memperpanjang data!");
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (id) => {
        const approved = await confirm({
            title: 'Arsipkan Transaksi',
            message: 'Apakah Anda yakin ingin menghapus atau mengarsipkan data ini?',
            confirmLabel: 'Ya, Arsipkan',
            tone: 'danger',
        });

        if (!approved) return;

        try {
            setLoading(true);
            await axios.delete(`http://localhost:8000/api/peminjaman/${id}`);
            alert("Data berhasil dihapus dari daftar aktif!");
            fetchData();
        } catch (error) {
            alert("Gagal menghapus data!");
            console.error(error);
        } finally {
            setLoading(false);
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
                    <h1 className="text-2xl font-bold font-montserrat">Riwayat Peminjaman</h1>
                    <p className="text-sm text-gray-500">Data keluar masuk buku </p>
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
                            <th className="p-4">Jatuh Tempo</th>
                            <th className="p-4 text-center">Status</th>
                            <th className="p-4 text-center">Aksi</th> 
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {data.map((item, i) => (
                            <tr key={i} className="text-sm hover:bg-blue-50/30 transition-colors">
                                <td className="p-4 font-bold">{item.nama_peminjam}</td>
                                <td className="p-4 text-[#265F9C] font-medium">{item.judul_buku}</td>
                                <td className="p-4 font-mono text-xs">{item.tgl_peminjaman ? item.tgl_peminjaman.split(' ')[0] : '-'}</td>
                                <td className="p-4">
                                    <p className={`font-mono text-xs ${item.isOverdue ? 'text-red-600 font-bold' : ''}`}>
                                        {item.tgl_harus_kembali ? item.tgl_harus_kembali.split(' ')[0] : '-'}
                                    </p>
                                    {item.jumlah_perpanjangan > 0 && (
                                        <p className="text-[10px] text-blue-600 font-bold">Ext: {item.jumlah_perpanjangan}x</p>
                                    )}
                                </td>
                                <td className="p-4 text-center">
                                    <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase ${
                                        item.status_peminjaman === 'Dipinjam' 
                                        ? (item.isOverdue ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-orange-50 text-orange-600 border border-orange-100')
                                        : 'bg-green-50 text-green-600 border border-green-100'
                                    }`}>
                                        {item.isOverdue && item.status_peminjaman === 'Dipinjam' ? 'Terlambat' : item.status_peminjaman}
                                    </span>
                                </td>
                                <td className="p-4 text-center">
                                    <div className="flex gap-2 justify-center">
                                        {item.canExtend && isPustakawan(user) && (
                                            <button 
                                                onClick={() => handlePerpanjang(item.id_peminjaman, item.judul_buku)}
                                                className="bg-blue-600 text-white hover:bg-blue-700 px-3 py-1 rounded-lg text-[10px] font-bold shadow-sm transition-all"
                                            >
                                                PERPANJANG
                                            </button>
                                        )}

                                        <button 
                                            onClick={() => handleUpdate(item.id_peminjaman, item.status_peminjaman)}
                                            className="bg-white border border-gray-200 hover:border-[#265F9C] hover:text-[#265F9C] px-3 py-1 rounded-lg text-[10px] font-bold transition-all"
                                        >
                                            {item.status_peminjaman === 'Dipinjam' ? 'KEMBALI' : 'BATAL'}
                                        </button>

                                        <button 
                                            onClick={() => handleDelete(item.id_peminjaman)}
                                            className="bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white px-3 py-1 rounded-lg text-[10px] font-bold transition-all"
                                        >
                                            HAPUS
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {data.length === 0 && !loading && (
                    <div className="text-center py-10 text-gray-400 italic text-sm">Belum ada data transaksi.</div>
                )}
            </div>
            <ConfirmDialog />
        </div>
    );
};

export default RiwayatPinjamPanel;
