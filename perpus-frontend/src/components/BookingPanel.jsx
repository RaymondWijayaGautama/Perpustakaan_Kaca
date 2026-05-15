import React, { useState, useEffect } from 'react';
import axios from 'axios';

const BookingPanel = ({ userRole, userId }) => {
    const [bookings, setBookings] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [newBooking, setNewBooking] = useState({ id_cp_koleksi: '', id_siswa_tetap: '' });

    const fetchBookings = async () => {
        setLoading(true);
        try {
            // FIX: Pastikan userId dibersihkan dari karakter aneh (misal: "1:1" jadi "1")
            const cleanUserId = userId ? String(userId).split(':')[0] : null;

            const response = await axios.get('http://localhost:8000/api/bookings', {
                params: { 
                    role: userRole, 
                    id_siswa_tetap: cleanUserId 
                }
            });
            setBookings(response.data.data || []);
        } catch (error) {
            console.error("Gagal load data booking:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const res = await axios.post('http://localhost:8000/api/bookings/store', newBooking);
            alert(res.data.message);
            setShowForm(false);
            setNewBooking({ id_cp_koleksi: '', id_siswa_tetap: '' }); 
            fetchBookings();
        } catch (err) {
            alert(err.response?.data?.message || "Gagal membuat booking");
        }
    };

    const handleCancel = async (id) => {
        if (!window.confirm("Yakin ingin membatalkan booking ini?")) return;
        try {
            const res = await axios.put(`http://localhost:8000/api/bookings/cancel/${id}`);
            alert(res.data.message);
            fetchBookings();
        } catch (err) {
            alert("Gagal membatalkan booking");
        }
    };

    useEffect(() => {
        if (userId || userRole === 'pustakawan') {
            fetchBookings();
        }
    }, [userRole, userId]);

    if (loading) return <div className="p-5 text-center font-bold text-[#265F9C]">Memuat data...</div>;

    return (
        <div className="bg-white p-6 rounded-lg shadow-md mt-4">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-xl font-bold text-gray-800">
                    {userRole === 'pustakawan' ? 'Semua Data Booking Buku' : 'Riwayat Booking Saya'}
                </h2>
                {userRole === 'pustakawan' && (
                    <button onClick={() => setShowForm(!showForm)} className="bg-[#265F9C] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition-all">
                        {showForm ? '✖ Batal' : '➕ Tambah Booking'}
                    </button>
                )}
            </div>

            {showForm && (
                <form onSubmit={handleSubmit} className="mb-8 p-6 bg-blue-50 rounded-xl border-2 border-dashed border-blue-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="flex flex-col gap-1">
                        <label className="text-xs font-black text-[#265F9C] uppercase">ID Copy Buku</label>
                        <input 
                            type="number" value={newBooking.id_cp_koleksi} placeholder="Contoh: 242" 
                            className="p-2 border rounded-lg outline-none focus:ring-2 focus:ring-[#265F9C]" required
                            onChange={e => setNewBooking({...newBooking, id_cp_koleksi: e.target.value})}
                        />
                    </div>
                    <div className="flex flex-col gap-1">
                        <label className="text-xs font-black text-[#265F9C] uppercase">NISN Siswa</label>
                        <input 
                            type="text" value={newBooking.id_siswa_tetap} placeholder="Masukkan NISN..." 
                            className="p-2 border rounded-lg outline-none focus:ring-2 focus:ring-[#265F9C]" required
                            onChange={e => setNewBooking({...newBooking, id_siswa_tetap: e.target.value})}
                        />
                    </div>
                    <div className="flex items-end">
                        <button type="submit" className="w-full bg-green-600 text-white font-bold py-2 rounded-lg hover:bg-green-700 shadow-md">Simpan Booking</button>
                    </div>
                </form>
            )}

            <div className="overflow-x-auto">
                <table className="min-w-full text-left text-sm">
                    <thead className="uppercase border-b-2 text-gray-600 bg-gray-50 font-black">
                        <tr>
                            <th className="px-4 py-3">ID</th>
                            <th className="px-4 py-3">Judul Buku</th>
                            {userRole === 'pustakawan' && <th className="px-4 py-3">Pemesan</th>}
                            <th className="px-4 py-3 text-center">Status</th>
                            <th className="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {bookings.length > 0 ? (
                            bookings.map((item) => (
                                <tr key={item.id_booking} className="border-b hover:bg-gray-50 transition-colors">
                                    <td className="px-4 py-3 font-mono text-[#265F9C] font-bold">#{item.id_booking}</td>
                                    <td className="px-4 py-3 font-semibold">{item.judul_koleksi}</td>
                                    {userRole === 'pustakawan' && <td className="px-4 py-3">{item.nama_siswa_tetap}</td>}
                                    <td className="px-4 py-3 text-center">
                                        <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase border
                                            ${String(item.status_booking).toUpperCase() === 'AKTIF' ? 'bg-green-50 text-green-700 border-green-200' : 
                                              String(item.status_booking).toUpperCase() === 'DIBATALKAN' ? 'bg-red-50 text-red-700 border-red-200' : 
                                              'bg-gray-50 text-gray-600 border-gray-200'}`}>
                                            {item.status_booking}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        {String(item.status_booking).toUpperCase() === 'AKTIF' && (
                                            <button onClick={() => handleCancel(item.id_booking)} className="text-red-600 hover:bg-red-50 px-3 py-1 rounded-lg font-bold text-xs border border-red-100">
                                                Cancel
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan={5} className="px-4 py-12 text-center text-gray-400 italic">Belum ada data booking.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default BookingPanel;