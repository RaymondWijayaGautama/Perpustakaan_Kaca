import React, { useState, useEffect } from 'react';

/**
 * Komponen JamDatang
 * Bagian dari: Transaksi Kunjungan Perpus
 * Fungsi: Mencatat jam datang pengunjung (Check-in)
 * Aktor: Pemustaka
 * 
 * Konstrain:
 * 1. Waktu tercatat otomatis oleh sistem.
 * 2. Hanya bisa diinput saat datang.
 * 3. Satu user hanya bisa check-in sekali per hari.
 */

const JamDatang = () => {
  const [status, setStatus] = useState('idle'); // idle, loading, success, error
  const [jamDatang, setJamDatang] = useState(null);
  const [currentTime, setCurrentTime] = useState(new Date());
  const [message, setMessage] = useState('');

  // Update jam real-time setiap detik untuk tampilan sebelum check-in
  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentTime(new Date());
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  // Pengecekan status check-in hari ini saat komponen dimuat
  useEffect(() => {
    const checkStatus = () => {
      const lastCheckInDate = localStorage.getItem('last_checkin_date');
      const today = new Date().toLocaleDateString('en-CA'); // Format YYYY-MM-DD

      if (lastCheckInDate === today) {
        setStatus('success');
        setJamDatang(localStorage.getItem('last_checkin_time'));
        setMessage('Anda sudah melakukan check-in hari ini.');
      }
    };

    checkStatus();
  }, []);

  const handleCheckIn = async () => {
    setStatus('loading');
    
    // Simulasi proses ke server
    setTimeout(() => {
      const now = new Date();
      const timeString = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
      });
      const dateString = now.toLocaleDateString('en-CA');

      // Simpan ke localStorage (Simulasi Database Client-side)
      localStorage.setItem('last_checkin_date', dateString);
      localStorage.setItem('last_checkin_time', timeString);

      setJamDatang(timeString);
      setStatus('success');
      setMessage('Check-in berhasil dicatat secara otomatis.');
    }, 800);
  };

  return (
    <div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow-lg space-y-6 border border-gray-100">
      <div className="text-center border-b pb-4">
        <h2 className="text-2xl font-extrabold text-blue-900">Presensi Perpustakaan</h2>
        <p className="text-sm font-medium text-blue-600 uppercase tracking-wider mt-1">Aktor: Pemustaka</p>
      </div>

      <div className="flex flex-col items-center justify-center py-4">
        {status === 'idle' && (
          <div className="text-center space-y-6 w-full">
            <div className="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
              <p className="text-gray-500 text-xs uppercase font-semibold">Waktu Sistem Saat Ini</p>
              <p className="text-4xl font-mono font-bold text-gray-800">
                {currentTime.toLocaleTimeString('id-ID')}
              </p>
              <p className="text-gray-400 text-xs mt-1">
                {currentTime.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
              </p>
            </div>
            
            <button
              onClick={handleCheckIn}
              className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-blue-200 shadow-xl flex items-center justify-center space-x-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              <span>Catat Jam Datang</span>
            </button>
          </div>
        )}

        {status === 'loading' && (
          <div className="flex flex-col items-center space-y-3 py-8">
            <div className="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-blue-600"></div>
            <span className="text-blue-600 font-medium animate-pulse">Sinkronisasi waktu sistem...</span>
          </div>
        )}

        {status === 'success' && (
          <div className="text-center p-6 bg-green-50 rounded-2xl border border-green-100 w-full shadow-inner">
            <div className="mb-4 inline-flex items-center justify-center w-12 h-12 bg-green-100 text-green-600 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <p className="text-green-800 font-bold text-lg">{message}</p>
            <div className="mt-4 p-3 bg-white rounded-xl shadow-sm border border-green-50">
              <span className="text-gray-400 text-xs uppercase font-bold tracking-widest">Jam Kedatangan</span>
              <p className="text-4xl font-mono font-black text-blue-700">{jamDatang}</p>
            </div>
            <p className="text-[10px] text-gray-400 mt-6 italic leading-tight">
              Sesuai kebijakan: Satu pemustaka hanya diperbolehkan melakukan satu kali check-in per hari kalender.
            </p>
          </div>
        )}
      </div>

      <div className="bg-blue-50 p-4 rounded-lg">
        <h4 className="text-xs font-bold text-blue-800 uppercase mb-2 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" className="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
          </svg>
          Aturan Sistem (Konstrain)
        </h4>
        <ul className="text-[11px] text-blue-700 space-y-1.5 leading-relaxed">
          <li className="flex items-start">
            <span className="mr-1.5">•</span>
            <span><strong>Otomatis:</strong> Waktu diambil dari server/sistem saat tombol ditekan.</span>
          </li>
          <li className="flex items-start">
            <span className="mr-1.5">•</span>
            <span><strong>Real-time:</strong> Hanya bisa diinput pada saat kedatangan fisik.</span>
          </li>
          <li className="flex items-start">
            <span className="mr-1.5">•</span>
            <span><strong>Limitasi:</strong> Validasi 1x check-in per hari untuk setiap akun Pemustaka.</span>
          </li>
        </ul>
      </div>
    </div>
  );
};

export default JamDatang;