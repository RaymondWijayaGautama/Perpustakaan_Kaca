import { useState } from 'react';
import axios from 'axios';
import InputField from '../components/InputField'; 

const Login = ({ setLoggedInUser }) => {
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [fieldErrors, setFieldErrors] = useState({
    identifier: false,
    password: false
  });

  const digitsOnly = /^\d+$/.test(identifier);
  const resolvedRole = digitsOnly && identifier.length > 10 ? 'karyawan' : 'siswa';

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');
    setFieldErrors({ identifier: false, password: false });

    if (typeof window.grecaptcha === 'undefined') {
      setError("Layanan keamanan sedang dimuat...");
      return;
    }

    window.grecaptcha.ready(() => {
      window.grecaptcha.execute('6Lcdm60sAAAAAG99P_zv9F6efRaSemnBeL_iZ3D3', { action: 'login' })
        .then(async (token) => {
          try {
            const response = await axios.post('http://localhost:8000/api/login', {
              identifier, 
              password, 
              role: resolvedRole,
              'g-recaptcha-response': token
            });
            
            localStorage.setItem('token', response.data.token);
            setLoggedInUser(response.data.user);
          } catch (err) {
            const status = err.response?.status;
            const backendData = err.response?.data;
            const message = backendData?.message || 'Terjadi kesalahan.';
            const attemptsLeft = backendData?.attempts_left;

            // --- LOGIKA EXCEPTION FIELD ---
            if (status === 404) {
              // Identitas tidak ditemukan
              setFieldErrors({ identifier: true, password: false });
              setError(message);
            } else if (status === 401) {
              // Password salah
              setFieldErrors({ identifier: false, password: true });
              setError(attemptsLeft !== undefined ? `${message} (Sisa: ${attemptsLeft}x)` : message);
            } else {
              setError(message);
            }
          }
        });
    });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-brand-primary-500 font-roboto p-space-4">
      <div className="container max-w-6xl flex flex-col lg:flex-row items-center justify-between gap-10">
        
        {/* --- BAGIAN KIRI: INFORMASI --- */}
        <div className="flex-1 text-white hidden lg:block pr-10">
          <div className="bg-white p-3 w-fit rounded-medium mb-space-3 shadow-lg border-2 border-brand-secondary-500">
             <img 
                src="https://www.smabopkri2yk.sch.id/images/2021/10/13/logo_baru2__100x134.png" 
                alt="Logo SMK BOPKRI 2 Yogyakarta" 
                className="h-20 w-auto object-contain"
             />
          </div>
          <h1 className="text-[36px] font-bold font-montserrat leading-tight mb-1">Halaman Login</h1>
          <p className="text-[16px] opacity-90 font-roboto mb-10">SMK BOPKRI 2 Yogyakarta</p>
        </div>

        {/* --- BAGIAN KANAN: KARTU LOGIN --- */}
        <div className="bg-white p-space-5 rounded-medium shadow-elevation-2 max-w-md w-full">
          <div className="mb-space-4">
            <h2 className="text-neutral-900 text-[28px] font-bold font-montserrat leading-tight mb-2">Selamat Datang di KACA</h2>
            <p className="text-[#585858] text-[14px] font-roboto">
                Masuk ke dashboard untuk mengelola akun dan data anda.
            </p>
          </div>

          <form onSubmit={handleLogin} className="space-y-space-3">
            <InputField 
              label={resolvedRole === 'karyawan' ? "Identitas (NIP)" : "Identitas (NISN)"}
              type="text"
              identifier="identifier"
              value={identifier}
              onChange={(e) => {
                setIdentifier(e.target.value);
                setFieldErrors({ identifier: false, password: false });
              }}
              placeholder="Masukkan identitas anda"
              // Tambahkan props error jika InputField mendukungnya
              isError={fieldErrors.identifier} 
            />

            <InputField 
              label="Kata Kunci"
              type="password"
              identifier="password"
              value={password}
              onChange={(e) => {
                setPassword(e.target.value);
                setFieldErrors({ identifier: false, password: false });
              }}
              placeholder="Masukkan kata sandi"
              // Tambahkan props error jika InputField mendukungnya
              isError={fieldErrors.password}
            />

            <button 
              type="submit"
              className="w-full bg-brand-primary-500 text-white py-space-2 rounded-medium font-bold font-montserrat 
                         hover:shadow-elevation-2 hover:brightness-110 active:scale-[0.98] transition-all duration-150 mt-space-3 text-sm tracking-widest"
            >
              MASUK
            </button>
          </form>

          {/* Alert Error Global & Exception */}
          {error && (
            <div className="mt-space-3 p-space-2 bg-red-50 border-l-4 border-[#C62828] text-[#C62828] text-[12px] font-roboto animate-pulse">
              <span className="font-bold block">{error.split(' (')[0]}</span>
              {error.includes('Sisa percobaan') && (
                  <span className="text-[10px] uppercase mt-1 block opacity-70 font-medium italic">
                      {error.match(/\(([^)]+)\)/)[1]}
                  </span>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Login;
