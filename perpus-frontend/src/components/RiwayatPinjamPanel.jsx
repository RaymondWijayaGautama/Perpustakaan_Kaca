import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import useConfirmDialog from './useConfirmDialog';

const RiwayatPinjamPanel = ({ user }) => {
    const { confirm, ConfirmDialog } = useConfirmDialog();
    const [data, setData] = useState([]);
    const [filterStatus, setFilterStatus] = useState('Semua');
    const [searchQuery, setSearchQuery] = useState('');
    const [tempSearch, setTempSearch] = useState('');
    const [sortConfig, setSortConfig] = useState({ key: 'tgl_peminjaman', direction: 'desc' });
    const [loading, setLoading] = useState(false);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editData, setEditData] = useState({
        id_peminjaman: '',
        nama_peminjam: '',
        judul_buku: '',
        tgl_pinjam: '',
        tgl_kembali: '', 
        status: '',
        denda: 0,
        keterangan: ''
    });

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
                params: { 
                    status: filterStatus,
                    search: searchQuery,
                    sort_by: sortConfig.key,
                    sort_order: sortConfig.direction
                }
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
    }, [filterStatus, searchQuery, sortConfig]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    const handleSearch = (e) => {
        if (e) e.preventDefault();
        setSearchQuery(tempSearch);
    };

    const toggleSort = (key) => {
        setSortConfig(prev => ({
            key,
            direction: prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc'
        }));
    };

    const SortIcon = ({ column }) => {
        if (sortConfig.key !== column) return <span className="ml-1 text-gray-300">↕</span>;
        return <span className="ml-1 text-[#265F9C]">{sortConfig.direction === 'asc' ? '↑' : '↓'}</span>;
    };

    const openEditModal = (item) => {
        setEditData({
            id_peminjaman: item.id_peminjaman,
            nama_peminjam: item.nama_peminjam,
            judul_buku: item.judul_buku,
            tgl_pinjam: item.tgl_peminjaman ? item.tgl_peminjaman.substring(0, 10) : '',
            tgl_kembali: item.tgl_harus_kembali ? item.tgl_harus_kembali.substring(0, 10) : '',
            status: item.status_peminjaman || 'Dipinjam',
            denda: item.denda_peminjaman || 0,
            keterangan: item.keterangan_peminjaman || ''
        });
        setIsModalOpen(true);
    };

    const handleSaveEdit = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            await axios.put(`http://localhost:8000/api/peminjaman/${editData.id_peminjaman}`, {
                tgl_pinjam: editData.tgl_pinjam,
                tgl_harus_kembali: editData.tgl_kembali,
                status_peminjaman: editData.status,
                denda_peminjaman: editData.denda,
                keterangan: editData.keterangan,
                kondisi_buku: 'Baik' // Required by backend validation
            });
            
            alert("Mantap! Data riwayat berhasil diperbarui secara detail.");
            setIsModalOpen(false);
            fetchData(); 
        } catch (error) {
            alert("Gagal menyimpan: " + (error.response?.data?.message || error.message));
        } finally {
            setLoading(false);
        }
    };

    const isPustakawan = (user) => {
        const jabatan = (user?.JABATAN_FUNGSIONAL || user?.jabatan_fungsional || '').toLowerCase();
        return jabatan === 'pustakawan';
    };

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
        <div className="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 text-[#1A1A1A] relative min-h-[500px]">
            {loading && (
                <div className="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
                    <div className="w-10 h-10 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
                </div>
            )}

            <div className="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Riwayat Peminjaman</h1>
                    <p className="text-sm text-gray-500">Data keluar masuk buku </p>
                </div>
                
                <div className="flex gap-2 w-full md:w-auto">
                    <form onSubmit={handleSearch} className="flex gap-2 flex-1 md:flex-initial">
                        <input 
                            type="text"
                            placeholder="Cari nama, judul, ISBN..."
                            className="p-3 border rounded-xl text-sm bg-gray-50 outline-none focus:ring-2 focus:ring-[#265F9C] w-full md:w-64"
                            value={tempSearch}
                            onChange={(e) => setTempSearch(e.target.value)}
                        />
                        <button type="submit" className="bg-[#265F9C] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-800 transition-all">
                            Cari
                        </button>
                    </form>
                    
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
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 uppercase text-[10px] font-black text-[#585858] border-b border-gray-200">
                        <tr>
                            <th className="p-4 cursor-pointer hover:bg-gray-100 transition-colors" onClick={() => toggleSort('nama_peminjam')}>
                                Peminjam <SortIcon column="nama_peminjam" />
                            </th>
                            <th className="p-4 cursor-pointer hover:bg-gray-100 transition-colors" onClick={() => toggleSort('judul_buku')}>
                                Judul Buku <SortIcon column="judul_buku" />
                            </th>
                            <th className="p-4 cursor-pointer hover:bg-gray-100 transition-colors" onClick={() => toggleSort('tgl_peminjaman')}>
                                Tgl Pinjam <SortIcon column="tgl_peminjaman" />
                            </th>
                            <th className="p-4 cursor-pointer hover:bg-gray-100 transition-colors" onClick={() => toggleSort('tgl_harus_kembali')}>
                                Jatuh Tempo <SortIcon column="tgl_harus_kembali" />
                            </th>
                            <th className="p-4 text-center cursor-pointer hover:bg-gray-100 transition-colors" onClick={() => toggleSort('status_peminjaman')}>
                                Status <SortIcon column="status_peminjaman" />
                            </th>
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
                                    <span className={`px-3 py-1.5 rounded-md text-[10px] font-black uppercase ${
                                        item.status_peminjaman === 'Dipinjam' 
                                        ? (item.isOverdue ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-orange-50 text-orange-600 border border-orange-100')
                                        : item.status_peminjaman === 'Dikembalikan' || item.status_peminjaman === 'Kembali'
                                        ? 'bg-green-50 text-green-600 border border-green-100'
                                        : 'bg-red-50 text-red-600 border border-red-100'
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
                                            onClick={() => openEditModal(item)}
                                            className="bg-white border border-gray-200 hover:border-[#265F9C] hover:text-[#265F9C] px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all shadow-sm"
                                        >
                                            EDIT
                                        </button>

                                        <button 
                                            onClick={() => handleUpdate(item.id_peminjaman, item.status_peminjaman)}
                                            className="bg-white border border-gray-200 hover:border-[#265F9C] hover:text-[#265F9C] px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all shadow-sm"
                                        >
                                            {item.status_peminjaman === 'Dipinjam' ? 'KEMBALI' : 'BATAL'}
                                        </button>

                                        <button 
                                            onClick={() => handleDelete(item.id_peminjaman)}
                                            className="bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all shadow-sm"
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
                    <div className="text-center py-10 text-gray-400 italic text-sm font-medium">Belum ada data transaksi.</div>
                )}
            </div>

            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 print:hidden">
                    <div className="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up">
                        <div className="bg-[#265F9C] px-6 py-4 flex justify-between items-center text-white">
                            <h3 className="text-lg font-bold font-montserrat tracking-wide">Edit Detail Peminjaman</h3>
                            <button onClick={() => setIsModalOpen(false)} className="text-white/70 hover:text-white transition-colors">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <form onSubmit={handleSaveEdit} className="p-6">
                            <div className="bg-blue-50/60 p-4 rounded-xl border border-blue-100 mb-6 grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Peminjam</p>
                                    <p className="font-bold text-[#265F9C] text-sm mt-1">{editData.nama_peminjam}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Buku yang Dipinjam</p>
                                    <p className="font-bold text-[#265F9C] text-sm mt-1">{editData.judul_buku}</p>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5">Tgl Pinjam</label>
                                    <input type="date" required className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={editData.tgl_pinjam} onChange={(e) => setEditData({...editData, tgl_pinjam: e.target.value})} />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5">Tgl Jatuh Tempo</label>
                                    <input type="date" required className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={editData.tgl_kembali} onChange={(e) => setEditData({...editData, tgl_kembali: e.target.value})} />
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5">Status Peminjaman</label>
                                    <select className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none font-bold text-gray-700" value={editData.status} onChange={(e) => setEditData({...editData, status: e.target.value})}>
                                        <option value="Dipinjam">Dipinjam</option>
                                        <option value="Dikembalikan">Dikembalikan</option>
                                        <option value="Terlambat">Terlambat</option>
                                        <option value="Hilang">Hilang</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-gray-700 mb-1.5">Denda (Rp)</label>
                                    <input type="number" min="0" placeholder="0" className="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none" value={editData.denda} onChange={(e) => setEditData({...editData, denda: e.target.value})} />
                                </div>
                            </div>

                            <div className="mb-6">
                                <label className="block text-xs font-bold text-gray-700 mb-1.5">Catatan / Keterangan Tambahan</label>
                                <textarea rows="2" placeholder="Contoh: Buku dikembalikan dalam kondisi lecek..." className="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#265F9C] outline-none resize-none" value={editData.keterangan} onChange={(e) => setEditData({...editData, keterangan: e.target.value})}></textarea>
                            </div>

                            <div className="flex justify-end gap-3 pt-5 border-t border-gray-100">
                                <button type="button" onClick={() => setIsModalOpen(false)} className="px-6 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg text-sm transition-colors">Batal</button>
                                <button type="submit" className="px-6 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white font-bold rounded-lg text-sm transition-colors shadow-md">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <ConfirmDialog />
        </div>
    );
};

export default RiwayatPinjamPanel;