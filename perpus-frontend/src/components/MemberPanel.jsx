const MemberPanel = ({ user, onLogout }) => {
  return (
    <div className="min-h-screen bg-[#F6F7F9] font-roboto p-6">
      <div className="max-w-6xl mx-auto">
        <nav className="flex justify-between items-center bg-white p-5 rounded-[8px] shadow-sm mb-8">
          <span className="font-montserrat font-bold text-[#265F9C] text-xl">KitaBaca</span>
          <div className="flex items-center gap-4">
            <span className="text-sm font-medium">{user.nama_siswa_tetap || user.nama_karyawan}</span>
            <button onClick={onLogout} className="bg-[#C62828] text-white px-4 py-2 rounded-[8px] text-sm font-bold">
              KELUAR
            </button>
          </div>
        </nav>

        <div className="bg-white p-10 rounded-[8px] shadow-sm relative overflow-hidden">
          <div className="absolute top-0 left-0 w-2 h-full bg-[#265F9C]"></div>
          <h1 className="font-montserrat text-3xl font-bold mb-2">Selamat Datang di Perpustakaan</h1>
          <p className="text-gray-500 max-w-md">Cari buku favoritmu dan kembangkan pengetahuanmu di SMK BOPKRI 2 Yogyakarta.</p>
          
          <div className="mt-10 flex gap-4">
            <input type="text" placeholder="Cari judul buku atau penulis..." className="flex-1 p-4 border rounded-[8px] outline-none focus:ring-2 focus:ring-[#265F9C]" />
            <button className="bg-[#265F9C] text-white px-8 rounded-[8px] font-bold">CARI</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MemberPanel; 