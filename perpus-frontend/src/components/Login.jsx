import { useState, useEffect } from 'react';
import axios from 'axios';
import InputField from '../components/InputField'; // Pastikan path sesuai dengan letak file

const Login = ({ setLoggedInUser }) => {
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState('karyawan'); 
  const [error, setError] = useState('');

  // Auto-detect role berdasarkan input identifier
  useEffect(() => {
    if (identifier.includes('@')) {
      setRole('karyawan');
    } else if (/^\d+$/.test(identifier)) {
      setRole('siswa');
    }
  }, [identifier]);

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');

    if (typeof window.grecaptcha === 'undefined') {
      setError("Layanan keamanan sedang dimuat, silakan tunggu sebentar.");
      return;
    }

    window.grecaptcha.ready(() => {
      window.grecaptcha.execute('6Lcdm60sAAAAAG99P_zv9F6efRaSemnBeL_iZ3D3', { action: 'login' })
        .then(async (token) => {
          try {
            const response = await axios.post('http://localhost:8000/api/login', {
              identifier, 
              password, 
              role,
              'g-recaptcha-response': token
            });
            
            localStorage.setItem('token', response.data.token);
            setLoggedInUser(response.data.user);
          } catch (err) {
            setError(err.response?.data?.message || 'Identitas atau Password salah.');
          }
        });
    });
  };

  return (
    /* Background Default: #F6F7F9  */
    <div className="min-h-screen flex items-center justify-center bg-background-default font-roboto">
      
      {/* Container: Radius Medium (8px) & Elevation-1 [cite: 137, 134] */}
      <div className="bg-white p-space-5 rounded-medium shadow-elevation-1 max-w-sm w-full border border-gray-100">
        
        <div className="text-center mb-space-4">
          {/* Brand Primary: #265F9C & Font Montserrat [cite: 30, 68] */}
          <h2 className="text-brand-primary-500 text-h3 font-bold font-montserrat tracking-tight">KitaBaca</h2>
          {/* Metadata Style: Roboto 12px [cite: 74, 100] */}
          <p className="text-text-low text-[12px] mt-1 uppercase tracking-[0.2em]">SMK BOPKRI 2 Yogyakarta</p>
        </div>

        <form onSubmit={handleLogin}>
          {/* Menggunakan Komponen Reusable InputField */}
          <InputField 
            label="Identitas"
            type="text"
            identifier="identifier"
            value={identifier}
            onChange={(e) => setIdentifier(e.target.value)}
          />

          <InputField 
            label="Kata Sandi"
            type="password"
            identifier="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />

          {/* Button: Montserrat Bold, Radius 8px [cite: 68, 137] */}
          <button 
            type="submit"
            className="w-full bg-brand-primary-500 text-text-invert py-space-2 rounded-medium font-bold font-montserrat 
                       hover:shadow-elevation-2 hover:translate-y-[-1px] active:scale-[0.98] transition-all mt-space-3"
          >
            MASUK
          </button>
        </form>

        {/* Error Respond: Warna #C62828 [cite: 30, 33] */}
        {error && (
          <div className="mt-space-3 p-space-1 bg-red-50 border-l-4 border-error text-text-alert text-[12px] font-bold font-roboto">
            {error}
          </div>
        )}
      </div>
    </div>
  );
};

export default Login;