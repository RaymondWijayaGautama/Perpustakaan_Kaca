import { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const fetchBooksRequest = async ({ search, sortBy, sortOrder, kategori, page }) => {
  return axios.get(`${API_BASE_URL}/buku`, {
    params: {
      search,
      sort_by: sortBy,
      sort_order: sortOrder,
      kategori,
      page,
      per_page: 8,
    },
  });
};

const MemberPanel = ({ user, onLogout }) => {
  const [books, setBooks] = useState([]);
  const [kategoriBuku, setKategoriBuku] = useState([]);
  const [search, setSearch] = useState('');
  const [sortBy, setSortBy] = useState('judul_koleksi');
  const [sortOrder, setSortOrder] = useState('asc');
  const [kategori, setKategori] = useState('');
  const [page, setPage] = useState(1);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const deferredSearch = useDeferredValue(search);

  useEffect(() => {
    const fetchKategori = async () => {
      try {
        const res = await axios.get(`${API_BASE_URL}/buku/kategori`);
        setKategoriBuku(res.data);
      } catch (err) {
        console.error(err);
      }
    };

    fetchKategori();
  }, []);

  useEffect(() => {
    const fetchBooks = async () => {
      setLoading(true);
      setError('');

      try {
        const res = await fetchBooksRequest({ search: deferredSearch, sortBy, sortOrder, kategori, page });
        setBooks(res.data.data);
        setPagination(res.data);
      } catch (err) {
        console.error(err);
        setError('Koleksi buku gagal dimuat.');
      } finally {
        setLoading(false);
      }
    };

    fetchBooks();
  }, [deferredSearch, sortBy, sortOrder, kategori, page]);

  const displayName = user.nama_siswa_tetap || user.nama_karyawan;
  const displayRole = user.nama_siswa_tetap ? 'Pemustaka Siswa' : 'Pemustaka Karyawan';

  return (
    <div className="min-h-screen bg-[#F6F7F9] font-roboto p-6">
      <div className="max-w-7xl mx-auto">
        <nav className="flex justify-between items-center bg-white p-5 rounded-[12px] shadow-sm mb-8 border border-slate-100">
          <div>
            <span className="font-montserrat font-bold text-[#265F9C] text-xl block">KitaBaca</span>
            <span className="text-xs uppercase tracking-[0.2em] text-[#7D7D7E]">{displayRole}</span>
          </div>
          <div className="flex items-center gap-4">
            <div className="text-right">
              <span className="text-sm font-semibold block">{displayName}</span>
              <span className="text-xs text-[#7D7D7E]">Katalog koleksi perpustakaan</span>
            </div>
            <button onClick={onLogout} className="bg-[#C62828] text-white px-4 py-2 rounded-[10px] text-sm font-bold">
              KELUAR
            </button>
          </div>
        </nav>

        <section className="bg-white p-8 rounded-[16px] shadow-sm border border-slate-100 overflow-hidden">
          <div className="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div className="max-w-2xl">
              <h1 className="font-montserrat text-3xl font-bold mb-2 text-[#1A1A1A]">Koleksi Buku</h1>
              <p className="text-[#585858] leading-7">
                Data koleksi diambil langsung dari database dan dapat difilter berdasarkan judul, penulis, kategori, serta diurutkan sesuai kebutuhan.
              </p>
              {search && (
                <p className="text-sm text-[#265F9C] mt-3">
                  Menampilkan hasil pencarian untuk keyword <span className="font-bold">{search}</span>.
                </p>
              )}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 w-full lg:max-w-4xl">
              <input
                type="text"
                placeholder="Cari judul atau penulis..."
                value={search}
                onChange={(event) => {
                  setSearch(event.target.value);
                  setPage(1);
                }}
                className="w-full p-3 border rounded-[10px] outline-none focus:ring-2 focus:ring-[#265F9C]"
              />
              <select
                value={kategori}
                onChange={(event) => {
                  setKategori(event.target.value);
                  setPage(1);
                }}
                className="w-full p-3 border rounded-[10px] outline-none focus:ring-2 focus:ring-[#265F9C] bg-white"
              >
                <option value="">Semua kategori</option>
                {kategoriBuku.map((item) => (
                  <option key={item.id_ref_koleksi} value={item.id_ref_koleksi}>
                    {item.deskripsi}
                  </option>
                ))}
              </select>
              <select
                value={sortBy}
                onChange={(event) => {
                  setSortBy(event.target.value);
                  setPage(1);
                }}
                className="w-full p-3 border rounded-[10px] outline-none focus:ring-2 focus:ring-[#265F9C] bg-white"
              >
                <option value="judul_koleksi">Urutkan: Judul</option>
                <option value="pengarang">Urutkan: Penulis</option>
                <option value="tahun">Urutkan: Tahun</option>
                <option value="kategori">Urutkan: Kategori</option>
              </select>
              <select
                value={sortOrder}
                onChange={(event) => {
                  setSortOrder(event.target.value);
                  setPage(1);
                }}
                className="w-full p-3 border rounded-[10px] outline-none focus:ring-2 focus:ring-[#265F9C] bg-white"
              >
                <option value="asc">A-Z / Lama-Baru</option>
                <option value="desc">Z-A / Baru-Lama</option>
              </select>
            </div>
          </div>

          {error && (
            <div className="mt-6 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm font-medium">
              {error}
            </div>
          )}

          {loading ? (
            <div className="py-16 text-center text-[#585858]">Memuat koleksi buku...</div>
          ) : (
            <>
              <div className="mt-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                {books.map((book) => (
                  <article key={book.ISBN} className="rounded-[14px] border border-slate-200 bg-slate-50 p-5 shadow-sm">
                    <div className="flex items-start justify-between gap-3">
                      <span className="inline-flex rounded-full bg-[#265F9C] px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-white">
                        {book.kategori}
                      </span>
                      <span className="rounded-full bg-white px-3 py-1 text-xs font-bold text-[#1A1A1A] border border-slate-200">
                        {book.jumlah_ekslempar} copy
                      </span>
                    </div>
                    <h2 className="mt-4 text-xl font-montserrat font-bold leading-8 text-[#1A1A1A] min-h-[96px]">
                      {book.judul_koleksi}
                    </h2>
                    <div className="space-y-2 text-sm text-[#585858] mt-4">
                      <p><span className="font-semibold text-[#1A1A1A]">Penulis:</span> {book.pengarang}</p>
                      <p><span className="font-semibold text-[#1A1A1A]">Penerbit:</span> {book.penerbit}</p>
                      <p><span className="font-semibold text-[#1A1A1A]">Tahun:</span> {book.tahun}</p>
                      <p><span className="font-semibold text-[#1A1A1A]">Rak:</span> {book.no_rak_buku || '-'}</p>
                    </div>
                    <div className="mt-4 border-t border-slate-200 pt-4">
                      <p className="text-[11px] font-mono text-[#7D7D7E] break-all">{book.ISBN}</p>
                    </div>
                  </article>
                ))}
              </div>

              {books.length === 0 && (
                <div className="mt-10 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-[#585858]">
                  Tidak ada koleksi yang sesuai dengan filter saat ini.
                </div>
              )}

              <div className="mt-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4 rounded-[14px] bg-slate-50 p-4 border border-slate-200">
                <div className="text-sm text-[#585858]">
                  Menampilkan <span className="font-bold text-[#1A1A1A]">{books.length}</span> buku pada halaman{' '}
                  <span className="font-bold text-[#1A1A1A]">{page}</span> dari{' '}
                  <span className="font-bold text-[#1A1A1A]">{pagination.last_page || 1}</span>.
                </div>
                <div className="flex items-center gap-3">
                  <button
                    disabled={page === 1}
                    onClick={() => setPage(page - 1)}
                    className="px-4 py-2 rounded-[10px] border bg-white text-sm font-bold disabled:opacity-50"
                  >
                    Prev
                  </button>
                  <button
                    disabled={page === pagination.last_page || !pagination.last_page}
                    onClick={() => setPage(page + 1)}
                    className="px-4 py-2 rounded-[10px] border bg-white text-sm font-bold disabled:opacity-50"
                  >
                    Next
                  </button>
                </div>
              </div>
            </>
          )}
        </section>
      </div>
    </div>
  );
};

export default MemberPanel; 
