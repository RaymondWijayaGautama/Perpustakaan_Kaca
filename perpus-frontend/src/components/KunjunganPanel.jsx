import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const KunjunganPanel = ({ user }) => {
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(false);
  const [checkingIn, setCheckingIn] = useState(false);
  const [checkingOut, setCheckingOut] = useState(false);
  const [message, setMessage] = useState('');
  const [isSuccess, setIsSuccess] = useState(false);

  const fetchHistory = async () => {
    setLoading(true);
    try {
      const response = await axios.get(`${API_BASE_URL}/kunjungan/history`);
      if (response.data && response.data.success) {
        setHistory(response.data.data);
      }
    } catch (err) {
      console.error('Failed to load history', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchHistory();
  }, []);

  const handleCheckIn = async () => {
    setCheckingIn(true);
    setMessage('');
    try {
      const response = await axios.post(`${API_BASE_URL}/kunjungan/checkin`);
      setIsSuccess(true);
      setMessage(response.data.message || 'Check-in berhasil!');
      fetchHistory(); // Refresh history
    } catch (err) {
      setIsSuccess(false);
      setMessage(err.response?.data?.message || 'Gagal melakukan check-in.');
    } finally {
      setCheckingIn(false);
    }
  };

  const handleCheckOut = async () => {
    setCheckingOut(true);
    setMessage('');
    try {
      const response = await axios.post(`${API_BASE_URL}/kunjungan/checkout`);
      setIsSuccess(true);
      setMessage(response.data.message || 'Check-out berhasil!');
      fetchHistory(); // Refresh history
    } catch (err) {
      setIsSuccess(false);
      setMessage(err.response?.data?.message || 'Gagal melakukan check-out.');
    } finally {
      setCheckingOut(false);
    }
  };

  // Determine current status based on today's history
  // Gunakan format lokal YYYY-MM-DD agar sinkron dengan WIB
  const now = new Date();
  const offset = now.getTimezoneOffset() * 60000;
  const localISOTime = new Date(now - offset).toISOString();
  const todayStr = localISOTime.split('T')[0];
  
  const todayVisit = history.find(log => log.START_KUNJUNGAN?.startsWith(todayStr));
  
  const status = !todayVisit 
    ? 'ready_in' 
    : (!todayVisit.END_KUNJUNGAN ? 'ready_out' : 'done');

  return (
    <div className="bg-white p-8 rounded-b-2xl rounded-tr-2xl shadow-sm border border-slate-100 mb-8 max-w-4xl mx-auto">
      <div className="text-center mb-8">
        <h2 className="text-2xl font-montserrat font-extrabold text-[#265F9C] mb-2">Check-In / Out Perpustakaan</h2>
        <p className="text-slate-500 text-sm">Catat jam kedatangan dan kepulangan Anda. Pastikan untuk menekan check-out sebelum meninggalkan perpustakaan.</p>
      </div>

      {message && (
        <div className={`mb-6 p-4 rounded-xl text-sm font-bold text-center border ${isSuccess ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'}`}>
          {message}
        </div>
      )}

      <div className="flex justify-center mb-10">
        {status === 'ready_in' && (
          <button
            onClick={handleCheckIn}
            disabled={checkingIn}
            className="relative group bg-gradient-to-r from-[#265F9C] to-[#1C4673] hover:from-[#1C4673] hover:to-[#122b47] text-white font-black text-xl px-12 py-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 active:scale-95 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span className="relative z-10 flex items-center gap-3">
              <span className="text-3xl">📍</span>
              {checkingIn ? 'MENCATAT...' : 'CHECK-IN HARI INI'}
            </span>
            <div className="absolute inset-0 h-full w-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
          </button>
        )}

        {status === 'ready_out' && (
          <button
            onClick={handleCheckOut}
            disabled={checkingOut}
            className="relative group bg-gradient-to-r from-orange-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-black text-xl px-12 py-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 active:scale-95 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span className="relative z-10 flex items-center gap-3">
              <span className="text-3xl">👋</span>
              {checkingOut ? 'MENCATAT...' : 'CHECK-OUT SEKARANG'}
            </span>
            <div className="absolute inset-0 h-full w-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
          </button>
        )}

        {status === 'done' && (
          <div className="bg-slate-100 border border-slate-200 text-slate-500 font-black text-lg px-12 py-6 rounded-3xl flex items-center gap-3">
            <span>✅</span> TERIMA KASIH ATAS KUNJUNGAN ANDA HARI INI
          </div>
        )}
      </div>

      <div className="mt-12 border-t border-slate-100 pt-8">
        <h3 className="text-lg font-bold text-[#1A1A1A] mb-4">Riwayat Kunjungan Anda</h3>
        
        {loading ? (
          <div className="flex justify-center items-center py-10">
            <div className="w-8 h-8 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin"></div>
          </div>
        ) : history.length > 0 ? (
          <div className="overflow-x-auto rounded-xl border border-slate-200">
            <table className="w-full text-sm text-left">
              <thead className="text-xs uppercase bg-slate-50 text-slate-500 font-bold tracking-wider">
                <tr>
                  <th className="px-6 py-4">No</th>
                  <th className="px-6 py-4">Tanggal Kunjungan</th>
                  <th className="px-6 py-4">Jam Datang</th>
                  <th className="px-6 py-4">Jam Pulang</th>
                </tr>
              </thead>
              <tbody>
                {history.map((log, index) => {
                  const parseDate = (dateStr) => {
                    if (!dateStr) return null;
                    // Jika string tidak mengandung 'Z' atau '+', asumsikan itu waktu lokal dari server
                    // Kita ganti spasi dengan 'T' agar formatnya standar
                    const isoStr = dateStr.replace(' ', 'T');
                    return new Date(isoStr);
                  };

                  const inObj = parseDate(log.START_KUNJUNGAN);
                  const outObj = parseDate(log.END_KUNJUNGAN);
                  
                  const dateStr = inObj ? inObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                  const inTimeStr = inObj ? inObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '-';
                  const outTimeStr = outObj ? outObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '-';
                  
                  return (
                    <tr key={log.ID_KUNJUNGAN || index} className="bg-white border-b border-slate-50 last:border-0 hover:bg-blue-50/50 transition-colors">
                      <td className="px-6 py-4 font-medium text-slate-400">{index + 1}</td>
                      <td className="px-6 py-4 font-bold text-[#265F9C]">{dateStr}</td>
                      <td className="px-6 py-4">
                        <span className="bg-green-50 text-green-700 px-2 py-1 rounded font-bold border border-green-200">{inTimeStr} WIB</span>
                      </td>
                      <td className="px-6 py-4">
                        {outObj ? (
                          <span className="bg-slate-50 text-slate-700 px-2 py-1 rounded font-bold border border-slate-200">{outTimeStr} WIB</span>
                        ) : (
                          <span className="text-slate-400 italic text-xs">Belum Pulang</span>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="text-center py-10 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
            <p className="text-slate-400 font-medium">Belum ada riwayat kunjungan.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default KunjunganPanel;
