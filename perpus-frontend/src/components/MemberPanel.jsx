import { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const fetchBooksRequest = async ({ judul, penulis, sortBy, sortOrder, kategori, page, activeTab }) => {
  const endpoint = activeTab === 'laporan' ? '/buku/laporan' : '/buku';
  
  return axios.get(`${API_BASE_URL}${endpoint}`, {
    params: {
      judul,       
      penulis,     
      sort_by: sortBy,
      sort_order: sortOrder,
      kategori: activeTab === 'laporan' ? '' : kategori,
      page,
      per_page: 8,
    },
  });
};

const MemberPanel = ({ user, onLogout }) => {
  const [activeTab, setActiveTab] = useState('buku');
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
            activeTab
        });
        setBooks(res.data.data || []);
        setPagination(res.data || {});
      } catch (err) {
        console.error(err);
        setError('Gagal memuat data. Pastikan API Laravel tidak 404.');
        // PENTING: Kosongkan data lama agar tidak nyangkut saat error
        setBooks([]); 
        setPagination({});
      } finally {
        setLoading(false);
      }
    };

    fetchBooks();
  }, [deferredJudul, deferredPenulis, sortBy, sortOrder, kategori, page, activeTab]);

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

        <div className="flex gap-2 mb-0">
            <button 
                onClick={() => handleTabChange('buku')}
                className={`px-8 py-3 font-montserrat font-bold text-sm rounded-t-2xl transition-all ${activeTab === 'buku' ? 'bg-white text-[#265F9C] border-t-2 border-x border-[#265F9C] shadow-sm relative z-10' : 'bg-slate-200/50 text-slate-500 hover:bg-slate-200'}`}
            >
                Koleksi Buku
            </button>
            <button 
                onClick={() => handleTabChange('laporan')}
                className={`px-8 py-3 font-montserrat font-bold text-sm rounded-t-2xl transition-all ${activeTab === 'laporan' ? 'bg-white text-[#265F9C] border-t-2 border-x border-[#265F9C] shadow-sm relative z-10' : 'bg-slate-200/50 text-slate-500 hover:bg-slate-200'}`}
            >
                Laporan PKL
            </button>
        </div>

        <section className="bg-white p-8 rounded-b-2xl rounded-tr-2xl shadow-sm border border-slate-100 mb-8">
            <div className="grid grid-cols-1 md:grid-cols-2 xl:flex gap-4">
                <div className="relative flex-1">
                    <input type="text" placeholder="Cari Judul..." value={searchJudul} onChange={(e) => { setSearchJudul(e.target.value); setPage(1); }} className="w-full p-3 pl-10 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#265F9C] transition-all" />
                    <span className="absolute left-3 top-3.5 text-slate-400">🔍</span>
                </div>
                
                <div className="relative flex-1">
                    <input type="text" placeholder={activeTab === 'laporan' ? "Nama Siswa..." : "Penulis..."} value={filterPenulis} onChange={(e) => { setFilterPenulis(e.target.value); setPage(1); }} className="w-full p-3 pl-10 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#2E7D32] bg-slate-50 focus:bg-white transition-all" />
                    <span className="absolute left-3 top-3.5 text-slate-400">👤</span>
                </div>

                {activeTab === 'buku' && (
                    <select value={kategori} onChange={(e) => { setKategori(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none focus:ring-2 focus:ring-[#265F9C] w-full xl:w-48">
                        <option value="">Semua Kategori</option>
                        {kategoriBuku.map((item) => (
                            <option key={item.id_ref_koleksi} value={item.id_ref_koleksi}>{item.deskripsi}</option>
                        ))}
                    </select>
                )}

                <select value={sortBy} onChange={(e) => { setSortBy(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none w-full xl:w-40 font-medium text-slate-600">
                    <option value="judul_koleksi">Urut: Judul</option>
                    <option value="pengarang">Urut: {activeTab === 'laporan' ? 'Siswa' : 'Penulis'}</option>
                    <option value="tahun">Urut: Tahun</option>
                </select>
                
                <select value={sortOrder} onChange={(e) => { setSortOrder(e.target.value); setPage(1); }} className="p-3 border border-slate-200 rounded-xl text-sm bg-white outline-none w-full xl:w-40 font-medium text-slate-600">
                    <option value="asc">A-Z / Terlama</option>
                    <option value="desc">Z-A / Terbaru</option>
                </select>
            </div>
        </section>

        {error && <div className="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-bold">{error}</div>}

        {loading ? (
          <div className="py-20 text-center">
            <div className="w-12 h-12 border-4 border-[#265F9C] border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-slate-500 font-bold animate-pulse">Menghubungkan Database...</p>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
              {books.map((book, index) => (
                <article 
                  key={book.id_cp_koleksi || book.ISBN || index} 
                  className="group bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-xl hover:border-[#265F9C]/30 transition-all duration-300 hover:-translate-y-2 relative overflow-hidden"
                >
                  <div className="flex justify-between items-start mb-4">
                    <span className="bg-[#265F9C] text-white text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-sm">
                        {book.kategori || (activeTab === 'laporan' ? 'Laporan PKL' : 'Umum')}
                    </span>
                    <span className={`text-[10px] font-bold px-2 py-1 rounded-lg border ${book.status_buku === 'Tersedia' ? 'text-green-600 bg-green-50 border-green-100' : 'text-orange-600 bg-orange-50 border-orange-100'}`}>
                        {book.status_buku || 'Tersedia'}
                    </span>
                  </div>
                  
                  <h2 className="font-montserrat font-extrabold text-[#1A1A1A] leading-tight mb-4 min-h-[48px] line-clamp-2 group-hover:text-[#265F9C] transition-colors">
                    {book.judul_koleksi}
                  </h2>

                  <div className="space-y-2 text-[11px] text-slate-500 border-t border-slate-50 pt-4">
                    
                    <p className="flex justify-between items-center gap-2">
                        <span className="font-bold text-slate-400 uppercase whitespace-nowrap">{activeTab === 'laporan' ? 'SISWA:' : 'PENULIS:'}</span> 
                        <span className="text-[#1A1A1A] font-semibold text-right truncate" title={book.nama_siswa_tetap || book.pengarang || 'Data Kosong'}>
                            {book.nama_siswa_tetap || book.pengarang || <span className="text-red-500 italic">Data Kosong</span>}
                        </span>
                    </p>
                    
                    <p className="flex justify-between">
                        <span className="font-bold text-slate-400 uppercase">TAHUN:</span> 
                        <span className="text-[#1A1A1A] font-semibold">{book.tahun}</span>
                    </p>
                    <p className="flex justify-between">
                        <span className="font-bold text-slate-400 uppercase">RAK:</span> 
                        <span className="text-[#265F9C] font-bold">{book.no_rak_buku || 'TBA'}</span>
                    </p>
                  </div>
                  
                  <div className="absolute -right-2 -bottom-2 w-12 h-12 bg-slate-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </article>
              ))}
            </div>

            {books.length === 0 && !loading && !error && (
              <div className="py-24 text-center bg-white rounded-2xl border-2 border-dashed border-slate-200">
                <div className="text-4xl mb-4">📚</div>
                <p className="text-slate-400 font-montserrat font-bold text-lg">Oops! Koleksi {activeTab} tidak ditemukan.</p>
                <button onClick={() => {setSearchJudul(''); setFilterPenulis('');}} className="mt-4 text-[#265F9C] text-sm font-bold underline">Reset Pencarian</button>
              </div>
            )}

            {books.length > 0 && (
              <div className="mt-10 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">
                  Halaman <span className="text-[#265F9C]">{page}</span> dari {pagination.last_page || 1}
                </span>
                <div className="flex gap-3">
                  <button disabled={page === 1} onClick={() => setPage(page - 1)} className="px-6 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-black disabled:opacity-40 transition-all uppercase tracking-tighter"> Prev </button>
                  <button disabled={page >= (pagination.last_page || 1)} onClick={() => setPage(page + 1)} className="px-6 py-2.5 bg-[#265F9C] hover:bg-[#1C4673] text-white rounded-xl text-xs font-black disabled:opacity-40 transition-all uppercase tracking-tighter shadow-md shadow-blue-100"> Next </button>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default MemberPanel; 