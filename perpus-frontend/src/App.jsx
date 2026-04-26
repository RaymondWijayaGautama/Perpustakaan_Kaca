import { useEffect, useState } from 'react';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';
import MemberPanel from './components/MemberPanel';

const USER_STORAGE_KEY = 'logged_in_user';
const TOKEN_STORAGE_KEY = 'token';

function App() {
  const [user, setUser] = useState(() => {
    // sessionStorage bertahan saat refresh, tapi hilang saat tab/browser ditutup.
    localStorage.removeItem(USER_STORAGE_KEY);
    localStorage.removeItem(TOKEN_STORAGE_KEY);

    const storedUser = sessionStorage.getItem(USER_STORAGE_KEY);

    if (!storedUser) {
      return null;
    }

    try {
      return JSON.parse(storedUser);
    } catch (error) {
      console.error('Gagal membaca sesi login tersimpan.', error);
      sessionStorage.removeItem(USER_STORAGE_KEY);
      return null;
    }
  });

  useEffect(() => {
    if (!user) {
      sessionStorage.removeItem(USER_STORAGE_KEY);
      return;
    }

    sessionStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
  }, [user]);

  const handleLogout = () => {
    sessionStorage.removeItem(TOKEN_STORAGE_KEY);
    sessionStorage.removeItem(USER_STORAGE_KEY);
    localStorage.removeItem(TOKEN_STORAGE_KEY);
    localStorage.removeItem(USER_STORAGE_KEY);
    setUser(null);
  };

  // 1. Jika BELUM login
  if (!user) {
    return <Login setLoggedInUser={setUser} />;
  }

  const isAdmin = user.jabatan_fungsional === 'Pustakawan';

  if (isAdmin) {
    return <AdminPanel user={user} onLogout={handleLogout} />;
  }

  // Jika bukan Pustakawan (Guru atau Siswa), masuk ke MemberPanel
  return <MemberPanel user={user} onLogout={handleLogout} />;
}

export default App;
