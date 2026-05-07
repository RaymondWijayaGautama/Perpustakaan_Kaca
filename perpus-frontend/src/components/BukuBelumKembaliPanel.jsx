import React, { useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const getUserNip = (user) => (
    user?.nip_karyawan ||
    user?.NIP_KARYAWAN ||
    user?.nip ||
    user?.NIP ||
    ''
);

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(String(value).replace(' ', 'T'));

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

const formatIsbn = (value) => {
    const digits = String(value || '').replace(/\D/g, '');

    if (digits.length !== 13) {
        return value || '-';
    }

    return `${digits.slice(0, 3)}-${digits.slice(3, 6)}-${digits.slice(6, 10)}-${digits.slice(10, 12)}-${digits.slice(12)}`;
};

const BukuBelumKembaliPanel = ({ user }) => {
    const [rows, setRows] = useState([]);
    const [summary, setSummary] = useState({ total: 0, maks_hari_terlambat: 0 });
    const [minDays, setMinDays] = useState(30);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(false);
    const [feedback, setFeedback] = useState({ type: '', message: '' });

    const loadRows = async () => {
        setLoading(true);
        setFeedback({ type: '', message: '' });

        try {
            const response = await axios.get(`${API_BASE_URL}/peminjaman/overdue`, {
                params: {
                    editor_nip_karyawan: getUserNip(user),
                    min_hari_terlambat: minDays,
                    search: search.trim(),
                },
            });

            setRows(response.data.data || []);
            setSummary(response.data.summary || { total: 0, maks_hari_terlambat: 0 });
        } catch (error) {
            console.error(error);
            setRows([]);
            setSummary({ total: 0, maks_hari_terlambat: 0 });
            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Gagal memuat data buku belum kembali.',
            });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadRows();
    }, []);

    const handleSubmit = (event) => {
        event.preventDefault();
        loadRows();
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Buku Belum Kembali</h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Data peminjaman aktif yang sudah melewati batas pengembalian dan belum memiliki tanggal kembali.
                    </p>
                </div>

                <div className="grid grid-cols-2 gap-3 min-w-[260px]">
                    <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3">
                        <p className="text-[10px] font-bold uppercase text-red-500">Total Overdue</p>
                        <p className="text-2xl font-black text-red-700">{summary.total}</p>
                    </div>
                    <div className="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                        <p className="text-[10px] font-bold uppercase text-amber-600">Terlama</p>
                        <p className="text-2xl font-black text-amber-700">{summary.maks_hari_terlambat} hari</p>
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end">
                <div className="lg:w-56">
                    <label className="mb-2 block text-xs font-bold uppercase text-[#585858]">Minimal Terlambat</label>
                    <div className="flex items-center rounded-xl border bg-gray-50 focus-within:ring-2 focus-within:ring-[#265F9C]">
                        <input
                            type="number"
                            min="1"
                            max="3650"
                            value={minDays}
                            onChange={(event) => setMinDays(event.target.value)}
                            className="w-full rounded-l-xl bg-transparent p-3 text-sm font-bold outline-none"
                        />
                        <span className="px-3 text-xs font-bold text-[#585858]">hari</span>
                    </div>
                </div>

                <div className="flex-1">
                    <label className="mb-2 block text-xs font-bold uppercase text-[#585858]">Cari</label>
                    <input
                        type="text"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Judul, ISBN, ID copy, ID transaksi, nama, atau NISN..."
                        className="w-full rounded-xl border bg-gray-50 p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]"
                    />
                </div>

                <button
                    type="submit"
                    disabled={loading}
                    className="rounded-xl bg-[#265F9C] px-5 py-3 text-sm font-bold text-white shadow hover:bg-blue-700 disabled:opacity-50"
                >
                    {loading ? 'Memuat...' : 'Tampilkan'}
                </button>
            </form>

            {feedback.message && (
                <div className="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {feedback.message}
                </div>
            )}

            <div className="overflow-x-auto rounded-xl border">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                        <tr>
                            <th className="p-4 w-16 text-center">No</th>
                            <th className="p-4">Buku</th>
                            <th className="p-4">Peminjam</th>
                            <th className="p-4">Tanggal</th>
                            <th className="p-4 text-center">Terlambat</th>
                            <th className="p-4">Status</th>
                            <th className="p-4">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan="7" className="p-10 text-center text-sm text-gray-500">Memuat data...</td>
                            </tr>
                        ) : rows.length === 0 ? (
                            <tr>
                                <td colSpan="7" className="p-10 text-center text-sm text-gray-500">Tidak ada buku yang melewati batas tersebut.</td>
                            </tr>
                        ) : (
                            rows.map((item, index) => (
                                <tr key={item.id_peminjaman} className="border-b last:border-b-0 text-sm hover:bg-red-50/30">
                                    <td className="p-4 text-center text-[#7D7D7E]">{index + 1}</td>
                                    <td className="p-4">
                                        <p className="font-bold text-[#1A1A1A]">{item.judul_buku}</p>
                                        <p className="mt-1 text-[11px] font-mono text-[#7D7D7E]">
                                            {formatIsbn(item.ISBN)} / Copy #{item.id_cp_koleksi}
                                        </p>
                                        <p className="mt-1 text-[11px] font-mono text-[#7D7D7E]">Transaksi #{item.id_peminjaman}</p>
                                    </td>
                                    <td className="p-4">
                                        <p className="font-bold">{item.nama_peminjam || '-'}</p>
                                        <p className="mt-1 text-[11px] font-mono text-[#7D7D7E]">{item.nisn_siswa || '-'}</p>
                                    </td>
                                    <td className="p-4 text-[#585858]">
                                        <p><span className="font-bold">Pinjam:</span> {formatDate(item.tgl_pinjam)}</p>
                                        <p className="mt-1"><span className="font-bold">Batas:</span> {formatDate(item.tgl_harus_kembali)}</p>
                                    </td>
                                    <td className="p-4 text-center">
                                        <span className="inline-flex rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-black text-red-700">
                                            {item.hari_terlambat} hari
                                        </span>
                                    </td>
                                    <td className="p-4">
                                        <span className="inline-flex rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[10px] font-bold uppercase text-amber-700">
                                            {item.status_peminjaman}
                                        </span>
                                        <p className="mt-2 text-xs text-[#7D7D7E]">Fisik: {item.status_buku || '-'}</p>
                                    </td>
                                    <td className="p-4 text-[#585858]">{item.nama_petugas || item.nip_karyawan || '-'}</td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default BukuBelumKembaliPanel;
