import React, { useState } from 'react';
import axios from 'axios';

const KalkulasiDendaBukuRusak = ({ bukuId, onkSukses }) => {
    const [nominal, setNominal] = useState('');
    const [kerusakan, setKerusakan] = useState('');
    const [loading, setLoading] = useState(false);
    const [hasil, setHasil] = useState(null);
    const [error, setError] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!bukuId) {
            setError("ISBN tidak ditemukan.");
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const response = await axios.post('http://localhost:8000/api/buku/denda-kerusakan', {
                id_buku: bukuId, // Berisi ISBN
                nominal_denda: nominal,
                jenis_kerusakan: kerusakan
            });
            setHasil(response.data);
            if (onkSukses) setTimeout(() => onkSukses(), 2000);
        } catch (err) {
            setError(err.response?.data?.message || "Gagal menyimpan laporan.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white p-8 rounded-2xl shadow-2xl border border-red-100 w-full max-w-md transform transition-all">
            <div className="flex items-center gap-4 mb-8 border-b border-red-50 pb-4">
                <div className="bg-red-500 p-3 rounded-xl shadow-lg shadow-red-200 text-white font-bold text-xl">⚠️</div>
                <div>
                    <h2 className="text-xl font-black text-gray-800 uppercase tracking-tight">Lapor Rusak</h2>
                    <p className="text-[10px] text-red-500 font-bold uppercase tracking-widest">ISBN: {bukuId}</p>
                </div>
            </div>

            {error && <div className="mb-4 p-3 bg-red-50 text-red-600 text-xs rounded-lg">{error}</div>}

            {hasil ? (
                <div className="bg-green-50 p-6 rounded-xl text-center border border-green-100">
                    <p className="text-green-800 font-bold">Laporan Berhasil Disimpan</p>
                    <p className="text-green-700 text-xs mt-2">{hasil.keterangan_denda}</p>
                    <p className="mt-4 text-2xl font-black text-red-600">{hasil.sanksi}</p>
                </div>
            ) : (
                <form onSubmit={handleSubmit} className="space-y-5">
                    <div>
                        <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">Kerusakan</label>
                        <input type="text" className="w-full p-4 bg-gray-50 border rounded-xl text-sm" placeholder="Misal: Sampul robek" value={kerusakan} onChange={(e) => setKerusakan(e.target.value)} required />
                    </div>
                    <div>
                        <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">Denda (Rp)</label>
                        <input type="number" className="w-full p-4 bg-gray-50 border rounded-xl font-mono" placeholder="0" value={nominal} onChange={(e) => setNominal(e.target.value)} required />
                    </div>
                    <button type="submit" disabled={loading} className={`w-full py-4 rounded-xl font-black text-white uppercase tracking-widest ${loading ? 'bg-gray-400' : 'bg-red-600 hover:bg-red-700'}`}>
                        {loading ? 'Memproses...' : 'Simpan Laporan'}
                    </button>
                </form>
            )}
        </div>
    );
};

export default KalkulasiDendaBukuRusak;