import { useEffect, useState } from 'react';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';
import MemberPanel from './components/MemberPanel';

const USER_STORAGE_KEY = 'logged_in_user';
const TOKEN_STORAGE_KEY = 'token';

function App() {
  const [user, setUser] = useState(null);

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('userRole'); // Tambahan: bersihkan role
    setUser(null);
  };

  // 1. Jika BELUM login
  if (!user) {
    return <Login setLoggedInUser={setUser} />;
  }

  // 2. Cek apakah user adalah Admin
  // PERBAIKAN: Gunakan JABATAN_FUNGSIONAL (huruf kapital)
  const isAdmin = user.JABATAN_FUNGSIONAL === 'Pustakawan';

  if (isAdmin) {
    return <AdminPanel user={user} onLogout={handleLogout} />;
  }

  // Jika bukan Pustakawan (Guru atau Siswa), masuk ke MemberPanel
  return <MemberPanel user={user} onLogout={handleLogout} />;
}

export default App;
