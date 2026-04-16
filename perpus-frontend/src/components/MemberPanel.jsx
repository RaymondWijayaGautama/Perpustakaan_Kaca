import { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

// Fungsi fetch disesuaikan dengan Active Tab
const fetchBooksRequest = async ({ judul, penulis, sortBy, sortOrder, kategori, page, activeTab }) => {
  // Jika tab laporan, tembak endpoint /buku/laporan
  const endpoint = activeTab === 'laporan' ? '/buku/laporan' : '/buku';
  
  return axios.get(`${API_BASE_URL}${endpoint}`, {
    params: {
      judul,       
      penulis,     
      sort_by: sortBy,
      sort_order: sortOrder,
      kategori: activeTab === 'laporan' ? '' : kategori, // Kategori dikosongkan jika mode laporan
      page,
      per_page: 8,
    },
  });
};

const MemberPanel = ({ user, onLogout }) => {
  const [activeTab, setActiveTab] = useState('buku'); // State Tab Switcher
  const [books, setBooks] = useState([]);
  const [kategoriBuku, setKategoriBuku] = useState([]);
  
  const [searchJudul, setSearchJudul] = useState('');
  const [filterPenulis, setFilterPenulis] = useState('');
  
  const [sortBy, setSortBy] = useState('judul_koleksi');
  const [sortOrder, setSortOrder] = useState('asc');
  const [kategori, setKategori] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  
  const deferredJudul = useDeferredValue(searchJudul);
  const deferredPenulis = useDeferredValue(filterPenulis);

  useEffect(() => {
    const fetchKategori = async () => {
      try {
        const res = await axios.get(`${API_BASE_URL}/buku/kategori`);
        setKategoriBuku(res.data || []);
      } catch (err) {
        console.error("Gagal load kategori:", err);
      }
    };
    fetchKategori();
  }, []);

  useEffect(() => {
    const fetchBooks = async () => {
      setLoading(true);
      setError('');
      try {
        const res = await fetchBooksRequest({ 
            judul: deferredJudul, 
            penulis: deferredPenulis, 
            sortBy, 
            sortOrder, 
            kategori, 
            page,
            activeTab // Kirim state tab ke fungsi fetch
        });
        setBooks(res.data.data || []);
        setPagination(res.data || {});
      } catch (err) {
        console.error(err);
        setError('Gagal memuat koleksi. Pastikan API Laravel sudah aktif.');
      } finally {
        setLoading(false);
      }
    };

    fetchBooks();
  }, [deferredJudul, deferredPenulis, sortBy, sortOrder, kategori, page, activeTab]);

  // Fungsi ganti tab dan reset halaman
  const handleTabChange = (tab) => {
      setActiveTab(tab);
      setPage(1);
      setSearchJudul('');
      setFilterPenulis('');
      setKategori('');
  };

  const displayName = user.nama_siswa_tetap || user.nama_karyawan || 'Member';
  const displayRole = user.nama_siswa_tetap ? 'Pemustaka Siswa' : 'Pemustaka Karyawan';

  return (
    <div className="min-h-screen bg-[#F6F7F9] font-roboto p-6">
      <div className="max-w-7xl mx-auto">
        <nav className="flex justify-between items-center bg-white p-5 rounded-xl shadow-sm mb-8 border border-slate-100">
          <div>
            <span className="font-montserrat font-bold text-[#265F9C] text-xl block leading-none">KitaBaca</span>
            <span className="text-[10px] uppercase tracking-[0.2em] text-[#7D7D7E] font-bold">{displayRole}</span>
          </div>
          <div className="flex items-center gap-4">
            <div className="text-right hidden sm:block">
              <span className="text-sm font-bold block text-[#1A1A1A]">{displayName}</span>
              <span className="text-[10px] text-[#7D7D7E]">ID: {user.nisn_siswa || user.nip_karyawan}</span>
            </div>
            <button onClick={onLogout} className="bg-[#C62828] hover:bg-red-800 text-white px-5 py-2 rounded-lg text-xs font-bold transition-all active:scale-95 shadow-sm">
              KELUAR
            </button>
          </div>
        </nav>

        {/* TAB SWITCHER */}
        <div className="flex gap-2 mb-6">
            <button 
                onClick={() => handleTabChange('buku')}
                className={`px-6 py-3 font-montserrat font-bold text-sm rounded-t-xl transition-all ${activeTab === 'buku' ? 'bg-white text-[#265F9C] border-t-2 border-x border-[#265F9C] shadow-sm translate-y-[1px] relative z-10' : 'bg-slate-200/50 text-slate-500 hover:bg-white border-transparent'}`}
            >
                Koleksi Buku
            </button>
            <button 
                onClick={() => handleTabChange('laporan')}
                className={`px-6 py-3 font-montserrat font-bold text-sm rounded-t-xl transition-all ${activeTab === 'laporan' ? 'bg-white text-[#265F9C] border-t-2 border-x border-[#265F9C] shadow-sm translate-y-[1px] relative z-10' : 'bg-slate-200/50 text-slate-500 hover:bg-white border-transparent'}`}
            >
                Laporan PKL
            </button>
        </div>

        <section className="bg-white p-6 rounded-b-2xl rounded-tr-2xl shadow-sm border border-slate-100 mb-6 relative z-0 -mt-6">
            <div className="flex flex-col xl:flex-row xl:items-center justify-between gap-6 pt-6">
                <div className="grid grid-cols-1 md:grid-cols-2 xl:flex gap-3 w-full">
                    <input type="text" placeholder="Judul..." value={searchJudul} onChange={(e) => { setSearchJudul(e.target.value); setPage(1); }} className="w-full xl:w-64 p-3 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#265F9C] transition-all" />
                    
                    {/* Tulisan placeholder disesuaikan dengan tab aktif */}
                    <input type="text" placeholder={activeTab === 'laporan' ? "Nama Siswa..." : "Penulis..."} value={filterPenulis} onChange={(e) => { setFilterPenulis(e.target.value); setPage(1); }} className="w-full xl:w-56 p-3 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32] bg-slate-50 focus:bg-white transition-all" />
                    
                    {/* Sembunyikan Filter Kategori jika di Tab Laporan */}
                    {activeTab === 'buku' && (
                      <select value={kategori} onChange={(e) => { setKategori(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none focus:ring-2 focus:ring-[#265F9C] w-full xl:w-48">
                          <option value="">Semua Kategori</option>
                          {kategoriBuku.map((item) => (
                              <option key={item.id_ref_koleksi} value={item.id_ref_koleksi}>{item.deskripsi}</option>
                          ))}
                      </select>
                    )}

                    <select value={sortBy} onChange={(e) => { setSortBy(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none w-full xl:w-40">
                        <option value="judul_koleksi">Urut: Judul</option>
                        <option value="pengarang">Urut: Penulis</option>
                        <option value="tahun">Urut: Tahun</option>
                    </select>
                    <select value={sortOrder} onChange={(e) => { setSortOrder(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none w-full xl:w-40">
                        <option value="asc">A-Z / Lama</option>
                        <option value="desc">Z-A / Baru</option>
                    </select>
                </div>
            </div>
        </section>

        {error && <div className="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-bold animate-pulse">{error}</div>}

        {loading ? (
          <div className="py-20 text-center">
            <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-slate-500 font-bold">Sinkronisasi {activeTab}...</p>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
              {books.map((book) => (
                <article key={book.ISBN} className="group bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-[#265F9C]/30 transition-all hover:-translate-y-1">
                  <div className="flex justify-between items-start mb-4">
                    <span className="bg-[#265F9C] text-white text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{book.kategori}</span>
                    <span className="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">Tersedia</span>
                  </div>
                  <h2 className="font-montserrat font-bold text-[#1A1A1A] leading-tight mb-4 min-h-[48px] line-clamp-2">{book.judul_koleksi}</h2>
                  <div className="space-y-2 text-[11px] text-slate-500 border-t border-slate-50 pt-4">
                    <p><span className="font-bold text-[#265F9C] mr-1">{activeTab === 'laporan' ? 'SISWA:' : 'PENULIS:'}</span> {book.pengarang}</p>
                    <p><span className="font-bold text-[#265F9C] mr-1">TAHUN:</span> {book.tahun}</p>
                    <p><span className="font-bold text-[#265F9C] mr-1">LOKASI RAK:</span> {book.no_rak_buku || 'TBA'}</p>
                  </div>
                </article>
              ))}
            </div>

            {books.length === 0 && (
              <div className="py-20 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <p className="text-slate-400 font-montserrat font-bold">Data {activeTab} tidak ditemukan.</p>
              </div>
            )}

            <div className="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
              <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Halaman {page} dari {pagination.last_page || 1}</span>
              <div className="flex gap-2">
                <button disabled={page === 1} onClick={() => setPage(page - 1)} className="px-6 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-black disabled:opacity-40 transition-all uppercase">Prev</button>
                <button disabled={page === pagination.last_page || !pagination.last_page} onClick={() => setPage(page + 1)} className="px-6 py-2 bg-[#265F9C] hover:bg-[#1C4673] text-white rounded-lg text-[10px] font-black disabled:opacity-40 transition-all uppercase">Next</button>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
};

export default MemberPanel;