const AdminPanel = ({ user, onLogout }) => {
  return (
    <div className="min-h-screen bg-[#F6F7F9] flex font-roboto">
      <aside className="w-64 bg-[#265F9C] text-white p-6 flex flex-col">
        <h2 className="font-montserrat font-bold text-xl mb-10 tracking-tight">KACA ADMIN</h2>
        <nav className="flex-1 space-y-4">
          <div className="hover:bg-white/10 p-2 rounded cursor-pointer transition-all">Manajemen Buku</div>
          <div className="hover:bg-white/10 p-2 rounded cursor-pointer transition-all">Data Anggota</div>
          <div className="hover:bg-white/10 p-2 rounded cursor-pointer transition-all">Sirkulasi</div>
        </nav>
        <button onClick={onLogout} className="mt-auto text-left p-2 text-red-200 hover:text-white font-bold">
          Keluar Sistem
        </button>
      </aside>

      <main className="flex-1 p-10">
        <header className="mb-8">
          <h1 className="font-montserrat text-3xl font-bold text-[#1A1A1A]">Dashboard Pustakawan</h1>
          <p className="text-gray-500">Selamat bekerja, {user.nama_karyawan}</p>
        </header>
        <div className="grid grid-cols-3 gap-6">
          <div className="bg-white p-6 rounded-[8px] shadow-sm border-t-4 border-[#265F9C]">
            <h3 className="text-sm font-bold text-gray-400">TOTAL BUKU</h3>
            <p className="text-3xl font-montserrat font-bold text-[#265F9C]">1,240</p>
          </div>
          {/* Tambahkan statistik lain di sini */}
        </div>
      </main>
    </div>
  );
};

export default AdminPanel;