import React, { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';
const EXPORT_URL = 'http://localhost:8000/pustakawan/buku/export';

const emptyEditForm = {
    ISBN: '',
    judul_koleksi: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    jumlah_ekslempar: '',
    no_rak_buku: '',
    keterangan_buku: '',
    id_ref_koleksi: '',
};

const fetchBooksRequest = async ({ search, sortBy, sortOrder, kategori, page }) => {
    return axios.get(`${API_BASE_URL}/buku`, {
        params: { search, sort_by: sortBy, sort_order: sortOrder, kategori, page, per_page: 10 },
    });
};

const ManajemenBukuPanel = ({ user }) => {
    const [books, setBooks] = useState([]);
    const [kategoriBuku, setKategoriBuku] = useState([]);
    const [bookSearch, setBookSearch] = useState('');
    const [bookSortBy, setBookSortBy] = useState('judul_koleksi');
    const [bookSortOrder, setBookSortOrder] = useState('asc');
    const [bookKategori, setBookKategori] = useState('');
    const [bookPage, setBookPage] = useState(1);
    const [bookPagination, setBookPagination] = useState({});
    const [loading, setLoading] = useState(false);
    const [showBarcodeModal, setShowBarcodeModal] = useState(false);
    const [barcodeData, setBarcodeData] = useState(null);
    const [isGenerating, setIsGenerating] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [selectedBook, setSelectedBook] = useState(null);
    const [editForm, setEditForm] = useState(emptyEditForm);
    const [formErrors, setFormErrors] = useState({});
    const [feedback, setFeedback] = useState({ type: '', message: '' });
    const deferredBookSearch = useDeferredValue(bookSearch);

    const loadBooks = async ({
        search = deferredBookSearch,
        sortBy = bookSortBy,
        sortOrder = bookSortOrder,
        kategori = bookKategori,
        page = bookPage,
    } = {}) => {
        setLoading(true);
        try {
            const res = await fetchBooksRequest({ search, sortBy, sortOrder, kategori, page });
            setBooks(res.data.data);
            setBookPagination(res.data);
        } catch (error) {
            console.error(error);
            setFeedback({ type: 'error', message: 'Gagal memuat data buku.' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        const run = async () => {
            setLoading(true);
            try {
                const res = await fetchBooksRequest({
                    search: deferredBookSearch,
                    sortBy: bookSortBy,
                    sortOrder: bookSortOrder,
                    kategori: bookKategori,
                    page: bookPage,
                });
                setBooks(res.data.data);
                setBookPagination(res.data);
            } catch (error) {
                console.error(error);
                setFeedback({ type: 'error', message: 'Gagal memuat data buku.' });
            } finally {
                setLoading(false);
            }
        };

        run();
    }, [bookPage, deferredBookSearch, bookSortBy, bookSortOrder, bookKategori]);

    useEffect(() => {
        const fetchKategori = async () => {
            try {
                const res = await axios.get(`${API_BASE_URL}/buku/kategori`);
                setKategoriBuku(res.data);
            } catch (error) {
                console.error(error);
            }
        };

        fetchKategori();
    }, []);

    const resetEditState = () => {
        setEditForm(emptyEditForm);
        setFormErrors({});
        setIsSaving(false);
    };

    const closeEditModal = () => {
        setShowEditModal(false);
        resetEditState();
    };

    const closeDeleteModal = () => {
        setShowDeleteModal(false);
        setSelectedBook(null);
        setIsDeleting(false);
    };

    const openEditModal = (buku) => {
        setFeedback({ type: '', message: '' });
        setFormErrors({});
        setEditForm({
            ISBN: buku.ISBN ?? '',
            judul_koleksi: buku.judul_koleksi ?? '',
            pengarang: buku.pengarang ?? '',
            penerbit: buku.penerbit ?? '',
            tahun: buku.tahun ?? '',
            jumlah_ekslempar: String(buku.jumlah_ekslempar ?? ''),
            no_rak_buku: buku.no_rak_buku ?? '',
            keterangan_buku: buku.keterangan_buku ?? '',
            id_ref_koleksi: String(buku.id_ref_koleksi ?? ''),
        });
        setShowEditModal(true);
    };

    const handleEditChange = (event) => {
        const { name, value } = event.target;
        setEditForm((current) => ({ ...current, [name]: value }));
        setFormErrors((current) => ({ ...current, [name]: undefined }));
    };

    const handleGenerateBarcode = async (buku) => {
        const identifier = buku.ISBN || buku.isbn || buku.id_mst_koleksi;
        if (!identifier) {
            setFeedback({ type: 'error', message: 'ISBN buku tidak ditemukan.' });
            return;
        }

        setShowBarcodeModal(true);
        setIsGenerating(true);
        setBarcodeData(null);
        setFeedback({ type: '', message: '' });

        try {
            const response = await axios.post(`${API_BASE_URL}/generate-barcode`, {
                isbn: identifier,
            });
            setBarcodeData(response.data);
        } catch (error) {
            console.error(error);
            setBarcodeData("<div class='text-red-500 p-4 text-center'>Gagal memuat barcode. Cek koneksi API.</div>");
        } finally {
            setIsGenerating(false);
        }
    };

    const openDeleteModal = (buku) => {
        setSelectedBook(buku);
        setShowDeleteModal(true);
        setFeedback({ type: '', message: '' });
    };

    const handleSaveEdit = async (event) => {
        event.preventDefault();
        setIsSaving(true);
        setFeedback({ type: '', message: '' });
        setFormErrors({});

        try {
            await axios.put(`${API_BASE_URL}/buku/${editForm.ISBN}`, {
                editor_nip_karyawan: user?.nip_karyawan,
                judul_koleksi: editForm.judul_koleksi.trim(),
                pengarang: editForm.pengarang.trim(),
                penerbit: editForm.penerbit.trim(),
                tahun: editForm.tahun.trim(),
                jumlah_ekslempar: Number(editForm.jumlah_ekslempar),
                no_rak_buku: editForm.no_rak_buku.trim(),
                keterangan_buku: editForm.keterangan_buku.trim(),
                id_ref_koleksi: Number(editForm.id_ref_koleksi),
            });

            closeEditModal();
            setFeedback({ type: 'success', message: 'Koleksi buku berhasil diperbarui.' });
            loadBooks();
        } catch (error) {
            console.error(error);

            if (error.response?.status === 422) {
                setFormErrors(error.response.data.errors || {});
            }

            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Perubahan buku gagal disimpan.',
            });
        } finally {
            setIsSaving(false);
        }
    };

    const handleDeleteBook = async () => {
        if (!selectedBook) {
            return;
        }

        setIsDeleting(true);
        setFeedback({ type: '', message: '' });

        try {
            await axios.delete(`${API_BASE_URL}/buku/${selectedBook.ISBN}`, {
                data: {
                    editor_nip_karyawan: user?.nip_karyawan,
                },
            });

            closeDeleteModal();
            setFeedback({ type: 'success', message: 'Koleksi buku berhasil dihapus.' });
            if (books.length === 1 && bookPage > 1) {
                setBookPage(bookPage - 1);
            } else {
                loadBooks();
            }
        } catch (error) {
            console.error(error);
            closeDeleteModal();
            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Koleksi buku gagal dihapus.',
            });
        } finally {
            setIsDeleting(false);
        }
    };

    const handleExportExcel = () => {
        const params = new URLSearchParams();

        if (bookSearch.trim()) {
            params.set('search', bookSearch.trim());
        }

        if (bookKategori) {
            params.set('kategori', bookKategori);
        }

        const queryString = params.toString();
        const targetUrl = queryString ? `${EXPORT_URL}?${queryString}` : EXPORT_URL;

        window.open(targetUrl, '_blank', 'noopener,noreferrer');
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Manajemen Buku</h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Pustakawan dapat mengubah metadata koleksi dan pembaruan langsung tersimpan ke database.
                    </p>
                    {bookSearch && (
                        <p className="text-xs text-[#265F9C] mt-2">
                            Pencarian aktif untuk keyword: <span className="font-bold">{bookSearch}</span>
                        </p>
                    )}
                </div>
                <button
                    type="button"
                    onClick={handleExportExcel}
                    className="bg-[#2E7D32] text-white px-5 py-3 rounded-xl text-sm font-bold shadow hover:bg-[#1b5e20] transition-colors whitespace-nowrap self-start"
                >
                    Export Excel
                </button>
            </div>

            <div className="mb-8 flex flex-wrap gap-3 justify-end">
                <input
                    type="text"
                    placeholder="Cari Judul atau Penulis..."
                    className="p-3 border rounded-xl text-sm outline-none min-w-[260px] focus:ring-2 focus:ring-[#265F9C] transition-all shadow-sm"
                    value={bookSearch}
                    onChange={(event) => {
                        setBookSearch(event.target.value);
                        setBookPage(1);
                    }}
                />
                <select
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
                    value={bookKategori}
                    onChange={(event) => {
                        setBookKategori(event.target.value);
                        setBookPage(1);
                    }}
                >
                    <option value="">Semua Kategori</option>
                    {kategoriBuku.map((kategori) => (
                        <option key={kategori.id_ref_koleksi} value={kategori.id_ref_koleksi}>
                            {kategori.deskripsi}
                        </option>
                    ))}
                </select>
                <select
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
                    value={bookSortBy}
                    onChange={(event) => {
                        setBookSortBy(event.target.value);
                        setBookPage(1);
                    }}
                >
                    <option value="judul_koleksi">Urut: Judul</option>
                    <option value="pengarang">Urut: Penulis</option>
                    <option value="tahun">Urut: Tahun</option>
                    <option value="kategori">Urut: Kategori</option>
                </select>
                <select
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
                    value={bookSortOrder}
                    onChange={(event) => {
                        setBookSortOrder(event.target.value);
                        setBookPage(1);
                    }}
                >
                    <option value="asc">A-Z / Lama-Baru</option>
                    <option value="desc">Z-A / Baru-Lama</option>
                </select>
            </div>

            {feedback.message && (
                <div className={`mb-6 rounded-xl border px-4 py-3 text-sm font-medium ${feedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'}`}>
                    {feedback.message}
                </div>
            )}

            {loading ? (
                <div className="text-center py-10 text-gray-500">Memuat data buku...</div>
            ) : (
                <>
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                            <tr>
                                <th className="p-4 w-16 text-center">No</th>
                                <th className="p-4">Judul</th>
                                <th className="p-4">Penulis</th>
                                <th className="p-4">Kategori</th>
                                <th className="p-4">Rak</th>
                                <th className="p-4 text-center">Copy</th>
                                <th className="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="font-roboto">
                            {books.map((buku, index) => (
                                <tr key={buku.ISBN} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                    <td className="p-4 text-center text-[#7D7D7E]">{(bookPage - 1) * 10 + index + 1}</td>
                                    <td className="p-4">
                                        <p className="font-bold text-[#1A1A1A]">{buku.judul_koleksi}</p>
                                        <p className="text-[11px] text-[#7D7D7E] font-mono mt-1">{buku.ISBN}</p>
                                    </td>
                                    <td className="p-4 text-[#585858]">{buku.pengarang}</td>
                                    <td className="p-4 text-[#585858]">{buku.kategori}</td>
                                    <td className="p-4 text-[#585858]">{buku.no_rak_buku || '-'}</td>
                                    <td className="p-4 text-center">
                                        <span className="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{buku.jumlah_ekslempar}</span>
                                    </td>
                                    <td className="p-4 text-center">
                                        <div className="flex justify-center gap-2">
                                            <button
                                                onClick={() => openEditModal(buku)}
                                                className="bg-amber-500 text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-amber-600 transition-colors"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                onClick={() => openDeleteModal(buku)}
                                                className="bg-[#C62828] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-red-700 transition-colors"
                                            >
                                                Hapus
                                            </button>
                                            <button
                                                onClick={() => handleGenerateBarcode(buku)}
                                                className="bg-[#265F9C] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-blue-700 transition-colors"
                                            >
                                                Cetak Barcode
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                        <button disabled={bookPage === 1} onClick={() => setBookPage(bookPage - 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">
                            Prev
                        </button>
                        <span className="text-xs font-bold text-[#585858] uppercase">
                            Halaman {bookPage} / {bookPagination.last_page || 1}
                        </span>
                        <button disabled={bookPage === bookPagination.last_page || !bookPagination.last_page} onClick={() => setBookPage(bookPage + 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">
                            Next
                        </button>
                    </div>
                </>
            )}

            {showEditModal && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-3xl relative">
                        <button onClick={closeEditModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-1 text-[#265F9C]">Ubah Koleksi Buku</h2>
                        <p className="text-sm text-[#585858] mb-6">
                            Validasi tetap berlaku dan perubahan akan langsung diperbarui ke database.
                        </p>

                        <form onSubmit={handleSaveEdit} className="space-y-5">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">ISBN</label>
                                    <input value={editForm.ISBN} disabled className="w-full rounded-xl border bg-gray-100 p-3 text-sm font-mono text-[#585858]" />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Kategori</label>
                                    <select name="id_ref_koleksi" value={editForm.id_ref_koleksi} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]">
                                        <option value="">Pilih kategori</option>
                                        {kategoriBuku.map((kategori) => (
                                            <option key={kategori.id_ref_koleksi} value={kategori.id_ref_koleksi}>
                                                {kategori.deskripsi}
                                            </option>
                                        ))}
                                    </select>
                                    {formErrors.id_ref_koleksi && <p className="mt-1 text-xs text-red-600">{formErrors.id_ref_koleksi[0]}</p>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Judul Buku</label>
                                    <input name="judul_koleksi" value={editForm.judul_koleksi} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.judul_koleksi && <p className="mt-1 text-xs text-red-600">{formErrors.judul_koleksi[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Pengarang</label>
                                    <input name="pengarang" value={editForm.pengarang} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.pengarang && <p className="mt-1 text-xs text-red-600">{formErrors.pengarang[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Penerbit</label>
                                    <input name="penerbit" value={editForm.penerbit} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.penerbit && <p className="mt-1 text-xs text-red-600">{formErrors.penerbit[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Tahun</label>
                                    <input name="tahun" value={editForm.tahun} onChange={handleEditChange} maxLength={4} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.tahun && <p className="mt-1 text-xs text-red-600">{formErrors.tahun[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Jumlah Eksemplar</label>
                                    <input name="jumlah_ekslempar" type="number" min="0" value={editForm.jumlah_ekslempar} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.jumlah_ekslempar && <p className="mt-1 text-xs text-red-600">{formErrors.jumlah_ekslempar[0]}</p>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Nomor Rak</label>
                                    <input name="no_rak_buku" value={editForm.no_rak_buku} onChange={handleEditChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.no_rak_buku && <p className="mt-1 text-xs text-red-600">{formErrors.no_rak_buku[0]}</p>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Keterangan</label>
                                    <textarea name="keterangan_buku" value={editForm.keterangan_buku} onChange={handleEditChange} rows="3" className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.keterangan_buku && <p className="mt-1 text-xs text-red-600">{formErrors.keterangan_buku[0]}</p>}
                                </div>
                            </div>

                            <div className="flex justify-end gap-3">
                                <button type="button" onClick={closeEditModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" disabled={isSaving} className="px-5 py-2 bg-[#265F9C] text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition-all disabled:opacity-50">
                                    {isSaving ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showDeleteModal && selectedBook && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg relative">
                        <button onClick={closeDeleteModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-2 text-[#C62828]">Hapus Koleksi Buku</h2>
                        <p className="text-sm text-[#585858] leading-6">
                            Anda akan menghapus koleksi <span className="font-bold text-[#1A1A1A]">{selectedBook.judul_koleksi}</span>.
                            Tindakan ini memerlukan konfirmasi pustakawan dan tidak bisa dilakukan jika buku sedang dipinjam.
                        </p>
                        <div className="mt-4 rounded-xl bg-gray-50 border p-4 text-sm">
                            <p><span className="font-bold">ISBN:</span> {selectedBook.ISBN}</p>
                            <p><span className="font-bold">Penulis:</span> {selectedBook.pengarang}</p>
                            <p><span className="font-bold">Kategori:</span> {selectedBook.kategori}</p>
                        </div>
                        <div className="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                onClick={closeDeleteModal}
                                className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                onClick={handleDeleteBook}
                                disabled={isDeleting}
                                className="px-5 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all disabled:opacity-50"
                            >
                                {isDeleting ? 'Menghapus...' : 'Ya, Hapus Buku'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {showBarcodeModal && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-2xl relative animate-fade-in-up">
                        <button onClick={() => setShowBarcodeModal(false)} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-4 text-[#265F9C] border-b pb-2">Cetak Barcode</h2>
                        <div className="flex flex-col items-center justify-center min-h-[200px] bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-4 mb-6">
                            {isGenerating ? (
                                <div className="text-gray-500 flex flex-col items-center">
                                    <span className="text-4xl animate-bounce mb-2">...</span>
                                    <p className="text-sm font-medium animate-pulse">Membuat Barcode...</p>
                                </div>
                            ) : (
                                <div dangerouslySetInnerHTML={{ __html: barcodeData }} />
                            )}
                        </div>

                        <div className="flex justify-end gap-3">
                            <button onClick={() => setShowBarcodeModal(false)} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                Tutup
                            </button>
                            <button onClick={() => window.print()} disabled={isGenerating} className="px-5 py-2 bg-[#2E7D32] text-white rounded-xl text-sm font-bold hover:bg-[#1b5e20] shadow-md transition-all disabled:opacity-50">
                                Print Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ManajemenBukuPanel;
