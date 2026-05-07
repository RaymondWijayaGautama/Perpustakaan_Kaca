import React, { useState, useEffect } from 'react';

/**
 * Komponen JamPulang
 * Fungsi: Mencatat jam pulang pengunjung (Check-out)
 * Konstrain: 
 * 1. Harus sudah check-in (JamDatang) terlebih dahulu.
 * 2. Hanya bisa dilakukan 1x per hari.
 */

const JamPulang = () => {
  const [status, setStatus] = useState('idle'); // idle, loading, success, error, no-checkin
  const [jamPulang, setJamPulang] = useState(null);
  const [currentTime, setCurrentTime] = useState(new Date());
  const [message, setMessage] = useState('');

  useEffect(() => {
    const timer = setInterval(() => setCurrentTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    const checkStatus = () => {
      const today = new Date().toLocaleDateString('en-CA');
      const lastCheckInDate = localStorage.getItem('last_checkin_date');
      const lastCheckOutDate = localStorage.getItem('last_checkout_date');

      // 1. Cek apakah sudah check-in hari ini?
      if (lastCheckInDate !== today) {
        setStatus('no-checkin');
        setMessage('Anda belum melakukan Presensi Datang hari ini.');
        return;
      }

      // 2. Cek apakah sudah check-out hari ini?
      if (lastCheckOutDate === today) {
        setStatus('success');
        setJamPulang(localStorage.getItem('last_checkout_time'));
        setMessage('Anda sudah melakukan check-out hari ini. Sampai jumpa besok!');
      }
    };

    checkStatus();
  }, []);

  const handleCheckOut = async () => {
    setStatus('loading');
    
    setTimeout(() => {
      const now = new Date();
      const timeString = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
      });
      const dateString = now.toLocaleDateString('en-CA');

      localStorage.setItem('last_checkout_date', dateString);
      localStorage.setItem('last_checkout_time', timeString);

      setJamPulang(timeString);
      setStatus('success');
      setMessage('Check-out berhasil! Terima kasih telah berkunjung.');
    }, 800);
  };

  return (
    <div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow-lg space-y-6 border border-gray-100">
      <div className="text-center border-b pb-4">
        <h2 className="text-2xl font-extrabold text-red-900">Presensi Kepulangan</h2>
        <p className="text-sm font-medium text-red-600 uppercase tracking-wider mt-1">Aktor: Pemustaka</p>
      </div>

      <div className="flex flex-col items-center justify-center py-4">
        {status === 'no-checkin' && (
          <div className="text-center p-6 bg-orange-50 rounded-2xl border border-orange-100 w-full">
            <div className="mb-4 inline-flex items-center justify-center w-12 h-12 bg-orange-100 text-orange-600 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <p className="text-orange-800 font-bold">{message}</p>
            <p className="text-xs text-orange-600 mt-2">Silahkan lakukan Presensi Datang terlebih dahulu di menu Jam Datang.</p>
          </div>
        )}

        {status === 'idle' && (
          <div className="text-center space-y-6 w-full">
            <div className="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
              <p className="text-gray-500 text-xs uppercase font-semibold">Waktu Sekarang</p>
              <p className="text-4xl font-mono font-bold text-gray-800">
                {currentTime.toLocaleTimeString('id-ID')}
              </p>
            </div>
            
            <button
              onClick={handleCheckOut}
              className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-red-200 shadow-xl flex items-center justify-center space-x-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span>Catat Jam Pulang</span>
            </button>
          </div>
        )}

        {status === 'loading' && (
          <div className="flex flex-col items-center space-y-3 py-8">
            <div className="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-red-600"></div>
            <span className="text-red-600 font-medium animate-pulse">Memproses waktu pulang...</span>
          </div>
        )}

        {status === 'success' && (
          <div className="text-center p-6 bg-red-50 rounded-2xl border border-red-100 w-full shadow-inner">
            <div className="mb-4 inline-flex items-center justify-center w-12 h-12 bg-red-100 text-red-600 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p className="text-red-800 font-bold text-lg">{message}</p>
            <div className="mt-4 p-3 bg-white rounded-xl shadow-sm border border-red-50">
              <span className="text-gray-400 text-xs uppercase font-bold tracking-widest">Jam Pulang Terdeteksi</span>
              <p className="text-4xl font-mono font-black text-red-700">{jamPulang}</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default JamPulang;