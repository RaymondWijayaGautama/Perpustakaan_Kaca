import { useEffect, useState } from 'react';
import axios from 'axios';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';
import MemberPanel from './components/MemberPanel';

const USER_STORAGE_KEY = 'logged_in_user';
const TOKEN_STORAGE_KEY = 'token';
const ROLE_STORAGE_KEY = 'userRole';
const ACCESS_LOG_STORAGE_KEY = 'access_log_id';

const readStoredUser = () => {
  try {
    const stored = localStorage.getItem(USER_STORAGE_KEY);
    return stored ? JSON.parse(stored) : null;
  } catch (error) {
    localStorage.removeItem(USER_STORAGE_KEY);
    return null;
  }
};

const getActorUsername = (actor) => (
  actor?.NIP_KARYAWAN
  || actor?.NISN_SISWA
  || actor?.identifier
  || actor?.USERNAME
  || 'SYSTEM'
);

const getActorRole = (actor) => (
  actor?.ROLE_LABEL
  || actor?.JABATAN_FUNGSIONAL
  || localStorage.getItem(ROLE_STORAGE_KEY)
  || 'Unknown'
);

function App() {
  const [user, setUserState] = useState(readStoredUser);

  const setUser = (nextUser) => {
    setUserState(nextUser);

    if (nextUser) {
      localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(nextUser));
      if (nextUser.ACCESS_LOG_ID) {
        localStorage.setItem(ACCESS_LOG_STORAGE_KEY, nextUser.ACCESS_LOG_ID);
      }
      return;
    }

    localStorage.removeItem(USER_STORAGE_KEY);
    localStorage.removeItem(ACCESS_LOG_STORAGE_KEY);
  };

  useEffect(() => {
    const token = localStorage.getItem(TOKEN_STORAGE_KEY);
    const accessLogId = user?.ACCESS_LOG_ID || localStorage.getItem(ACCESS_LOG_STORAGE_KEY);

    if (token) {
      axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    } else {
      delete axios.defaults.headers.common.Authorization;
    }

    if (user) {
      axios.defaults.headers.common['X-Actor-Username'] = getActorUsername(user);
      axios.defaults.headers.common['X-Actor-Role'] = getActorRole(user);

      if (accessLogId) {
        axios.defaults.headers.common['X-Access-Log-Id'] = accessLogId;
      }
    } else {
      delete axios.defaults.headers.common['X-Actor-Username'];
      delete axios.defaults.headers.common['X-Actor-Role'];
      delete axios.defaults.headers.common['X-Access-Log-Id'];
    }
  }, [user]);

  const handleLogout = async () => {
    const activeUser = user;

    try {
      await axios.post('http://localhost:8000/api/logout', {
        access_log_id: activeUser?.ACCESS_LOG_ID || localStorage.getItem(ACCESS_LOG_STORAGE_KEY),
        actor_username: getActorUsername(activeUser),
        actor_role: getActorRole(activeUser),
      });
    } catch (error) {
      console.error(error);
    } finally {
      localStorage.removeItem(TOKEN_STORAGE_KEY);
      localStorage.removeItem(ROLE_STORAGE_KEY);
      setUser(null);
    }
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
