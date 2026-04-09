import { useState } from 'react';
import Login from './components/Login';
import AdminPanel from './components/AdminPanel';
import MemberPanel from './components/MemberPanel';

function App() {
  const [user, setUser] = useState(null);
  console.log("Data User Login:", user);
  const handleLogout = () => {
    localStorage.removeItem('token');
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