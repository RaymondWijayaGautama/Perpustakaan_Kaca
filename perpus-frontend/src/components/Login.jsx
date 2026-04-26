
import { useState } from 'react';
import axios from 'axios';
// HAPUS: import { useNavigate } from 'react-router-dom';
import InputField from '../components/InputField'; 

const Login = ({ setLoggedInUser }) => {
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [fieldErrors, setFieldErrors] = useState({
    identifier: false,
    password: false
  });

  // HAPUS: const navigate = useNavigate();

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
            
            // Simpan data yang diperlukan
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('userRole', response.data.role); 
            
            // SET STATE INI AKAN OTOMATIS MENGUBAH TAMPILAN DI App.js
            setLoggedInUser(response.data.user);

            // HAPUS: Blok if(resolvedRole === 'karyawan') navigate(...)

          } catch (err) {
            const status = err.response?.status;
            const backendData = err.response?.data;
            const message = backendData?.message || 'Terjadi kesalahan.';
            
            const infoText = backendData?.info || ''; 
            const displayError = infoText ? `${message} | ${infoText}` : message;

            if (status === 404) {
              setFieldErrors({ identifier: true, password: false });
              setError(displayError);
            } else if (status === 401) {
              setFieldErrors({ identifier: false, password: true });
              setError(displayError);
            } else if (status === 429) {
              setFieldErrors({ identifier: true, password: true });
              setError(message);
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
              label={"Identitas"}
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
              label="Kata Sandi"
              type={showPassword ? 'text' : 'password'}
              identifier="password"
              value={password}
              onChange={(e) => {
                setPassword(e.target.value);
                setFieldErrors({ identifier: false, password: false });
              }}
              placeholder="Masukkan kata sandi"
              // Tambahkan props error jika InputField mendukungnya
              isError={fieldErrors.password}
              trailingContent={
                <button
                  type="button"
                  onClick={() => setShowPassword((current) => !current)}
                  className="inline-flex h-8 items-center justify-center gap-1.5 rounded-md px-1 text-[11px] font-black uppercase leading-none tracking-wide text-[#265F9C] hover:text-[#1C4673] focus:outline-none focus:ring-2 focus:ring-[#265F9C]/30"
                  aria-label={showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
                >
                  <svg
                    aria-hidden="true"
                    className="block h-[15px] w-[15px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    viewBox="0 0 24 24"
                  >
                    {showPassword ? (
                      <>
                        <path d="M3 3l18 18" />
                        <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                        <path d="M9.9 4.2A10.7 10.7 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-3.1 4.6" />
                        <path d="M6.6 6.6C3.5 8.7 2 12 2 12s3 8 10 8a10.8 10.8 0 0 0 4.4-.9" />
                      </>
                    ) : (
                      <>
                        <path d="M2 12s3-8 10-8 10 8 10 8-3 8-10 8S2 12 2 12Z" />
                        <circle cx="12" cy="12" r="3" />
                      </>
                    )}
                  </svg>
                  <span className="translate-y-px">{showPassword ? 'Tutup' : 'Lihat'}</span>
                </button>
              }
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
