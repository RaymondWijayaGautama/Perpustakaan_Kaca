import { useState } from 'react';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';
import MemberPanel from './components/MemberPanel';

const USER_STORAGE_KEY = 'logged_in_user';
const TOKEN_STORAGE_KEY = 'token';

function App() {
  const [user, setUser] = useState(() => {
    try {
      const savedUser = localStorage.getItem(USER_STORAGE_KEY);
      return savedUser ? JSON.parse(savedUser) : null;
    } catch (error) {
      localStorage.removeItem(USER_STORAGE_KEY);
      return null;
    }
  });

  const handleSetLoggedInUser = (nextUser) => {
    localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(nextUser));
    setUser(nextUser);
  };

  const handleLogout = () => {
    localStorage.removeItem(TOKEN_STORAGE_KEY);
    localStorage.removeItem('userRole'); // Tambahan: bersihkan role
    localStorage.removeItem(USER_STORAGE_KEY);
    localStorage.removeItem('admin_active_tab');
    localStorage.removeItem('member_active_tab');
    setUser(null);
  };

  // 1. Jika BELUM login
  if (!user) {
    return <Login setLoggedInUser={handleSetLoggedInUser} />;
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
