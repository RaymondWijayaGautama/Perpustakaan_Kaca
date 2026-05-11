import React, { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';
const EXPORT_URL = 'http://localhost:8000/pustakawan/buku/export';
const IMPORT_URL = 'http://localhost:8000/pustakawan/buku/import';

const emptyForm = {
    ISBN: '',
    judul_koleksi: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    jumlah_eksemplar: '1',
    no_rak_buku: '',
    keterangan_buku: '',
    id_ref_koleksi: '',
};

const fallbackCopyStatusOptions = ['Tersedia', 'Kembali', 'Rusak', 'Hilang', 'Nonaktif'];

const RequiredMark = () => <span className="text-red-500 ml-1">*</span>;

const normalizeIsbn = (value) => value.replace(/\D/g, '').slice(0, 13);

const getUserNip = (user) => (
    user?.nip_karyawan ||
    user?.NIP_KARYAWAN ||
    user?.nip ||
    user?.NIP ||
    ''
);

const formatIsbn = (value) => {
    const digits = normalizeIsbn(value);
    const groups = [3, 3, 4, 2, 1];
    const parts = [];
    let cursor = 0;

    for (const groupLength of groups) {
        if (cursor >= digits.length) {
            break;
        }

        parts.push(digits.slice(cursor, cursor + groupLength));
        cursor += groupLength;
    }

    return parts.join('-');
};

const fetchBooksRequest = async ({ search, sortBy, sortOrder, kategori, page }) => (
    axios.get(`${API_BASE_URL}/buku`, {
        params: { search, sort_by: sortBy, sort_order: sortOrder, kategori, page, per_page: 10 },
    })
);

const statusBadgeClass = (status) => {
    const normalized = String(status || '').toLowerCase();

    if (normalized === 'tersedia' || normalized === 'kembali') {
        return 'bg-green-50 text-green-700 border-green-200';
    }

    if (normalized === 'dipinjam') {
        return 'bg-amber-50 text-amber-700 border-amber-200';
    }

    if (normalized === 'rusak' || normalized === 'hilang') {
        return 'bg-red-50 text-red-700 border-red-200';
    }

    return 'bg-gray-100 text-gray-700 border-gray-200';
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
    const [feedback, setFeedback] = useState({ type: '', message: '' });

    const [showFormModal, setShowFormModal] = useState(false);
    const [formMode, setFormMode] = useState('create');
    const [formData, setFormData] = useState(emptyForm);
    const [formErrors, setFormErrors] = useState({});
    const [isSaving, setIsSaving] = useState(false);

    const [selectedBook, setSelectedBook] = useState(null);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const [showBarcodeModal, setShowBarcodeModal] = useState(false);
    const [barcodeData, setBarcodeData] = useState('');
    const [isGeneratingBarcode, setIsGeneratingBarcode] = useState(false);

    const [conditionBook, setConditionBook] = useState(null);
    const [showConditionModal, setShowConditionModal] = useState(false);
    const [bookCopies, setBookCopies] = useState([]);
    const [copyStatusOptions, setCopyStatusOptions] = useState(fallbackCopyStatusOptions);
    const [isLoadingCopies, setIsLoadingCopies] = useState(false);
    const [savingCopyId, setSavingCopyId] = useState(null);
    const [isCreatingCopy, setIsCreatingCopy] = useState(false);
    const [copyToDelete, setCopyToDelete] = useState(null);
    const [isDeletingCopy, setIsDeletingCopy] = useState(false);
    const [conditionFeedback, setConditionFeedback] = useState({ type: '', message: '' });

    const deferredBookSearch = useDeferredValue(bookSearch);
    const isYearSort = bookSortBy === 'tahun';

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
            setBooks(res.data.data || []);
            setBookPagination(res.data || {});
        } catch (error) {
            console.error(error);
            setFeedback({ type: 'error', message: 'Gagal memuat data buku.' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadBooks();
    }, [bookPage, deferredBookSearch, bookSortBy, bookSortOrder, bookKategori]);

    useEffect(() => {
        const fetchKategori = async () => {
            try {
                const res = await axios.get(`${API_BASE_URL}/buku/kategori`);
                setKategoriBuku(res.data || []);
            } catch (error) {
                console.error(error);
            }
        };

        fetchKategori();
    }, []);

    const resetForm = () => {
        setFormData(emptyForm);
        setFormErrors({});
        setIsSaving(false);
    };

    const closeFormModal = () => {
        setShowFormModal(false);
        setSelectedBook(null);
        resetForm();
    };

    const openCreateModal = () => {
        setFeedback({ type: '', message: '' });
        setFormMode('create');
        setSelectedBook(null);
        setFormData(emptyForm);
        setFormErrors({});
        setShowFormModal(true);
    };

    const openEditModal = (book) => {
        setFeedback({ type: '', message: '' });
        setFormMode('edit');
        setSelectedBook(book);
        setFormErrors({});
        setFormData({
            ISBN: formatIsbn(book.ISBN ?? ''),
            judul_koleksi: book.judul_koleksi ?? '',
            pengarang: book.pengarang ?? '',
            penerbit: book.penerbit ?? '',
            tahun: String(book.tahun ?? ''),
            jumlah_eksemplar: String(book.jumlah_eksemplar ?? '1'),
            no_rak_buku: book.no_rak_buku ?? '',
            keterangan_buku: book.keterangan_buku ?? '',
            id_ref_koleksi: String(book.id_ref_koleksi ?? ''),
        });
        setShowFormModal(true);
    };

    const handleFormChange = (event) => {
        const { name, value } = event.target;
        const nextValue = name === 'ISBN' ? formatIsbn(value) : value;
        setFormData((current) => ({ ...current, [name]: nextValue }));
        setFormErrors((current) => ({ ...current, [name]: undefined }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setIsSaving(true);
        setFormErrors({});
        setFeedback({ type: '', message: '' });

        const payload = {
            editor_nip_karyawan: getUserNip(user),
            ISBN: normalizeIsbn(formData.ISBN),
            judul_koleksi: formData.judul_koleksi.trim(),
            pengarang: formData.pengarang.trim(),
            penerbit: formData.penerbit.trim(),
            tahun: formData.tahun.trim(),
            jumlah_eksemplar: Number(formData.jumlah_eksemplar),
            no_rak_buku: formData.no_rak_buku.trim(),
            keterangan_buku: formData.keterangan_buku.trim(),
            id_ref_koleksi: Number(formData.id_ref_koleksi),
        };

        if (payload.ISBN.length !== 13) {
            setFormErrors({
                ISBN: ['ISBN harus terdiri dari 13 digit angka. Contoh: 978-602-8519-93-9.'],
            });
            setFeedback({
                type: 'error',
                message: 'Data buku gagal disimpan. ISBN harus berjumlah 13 digit.',
            });
            setIsSaving(false);
            return;
        }

        try {
            if (formMode === 'create') {
                await axios.post(`${API_BASE_URL}/buku`, payload);
                setFeedback({ type: 'success', message: 'Koleksi buku berhasil ditambahkan.' });
            } else {
                await axios.put(`${API_BASE_URL}/buku/${selectedBook.ISBN}`, payload);
                setFeedback({ type: 'success', message: 'Koleksi buku berhasil diperbarui.' });
            }

            closeFormModal();
            setBookPage(1);
            loadBooks({ page: 1 });
        } catch (error) {
            console.error(error);

            if (error.response?.status === 422) {
                setFormErrors(error.response.data.errors || {});
            }

            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Data buku gagal disimpan.',
            });
        } finally {
            setIsSaving(false);
        }
    };

    const openDeleteModal = (book) => {
        setSelectedBook(book);
        setShowDeleteModal(true);
        setFeedback({ type: '', message: '' });
    };

    const closeDeleteModal = () => {
        setSelectedBook(null);
        setShowDeleteModal(false);
        setIsDeleting(false);
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
                    editor_nip_karyawan: getUserNip(user),
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

    const handleGenerateBarcode = async (book) => {
        setShowBarcodeModal(true);
        setIsGeneratingBarcode(true);
        setBarcodeData('');
        setFeedback({ type: '', message: '' });

        try {
            const response = await axios.post(`${API_BASE_URL}/generate-barcode`, {
                isbn: book.ISBN,
                editor_nip_karyawan: getUserNip(user),
            });

            setBarcodeData(response.data);
        } catch (error) {
            console.error(error);
            setBarcodeData("<div class='text-red-500 p-4 text-center'>Gagal membuat barcode.</div>");
        } finally {
            setIsGeneratingBarcode(false);
        }
    };

    // Fungsi untuk men-generate barcode spesifik per copy fisik
    const handleGenerateCopyBarcode = async (copy) => {
        setShowBarcodeModal(true);
        setIsGeneratingBarcode(true);
        setBarcodeData('');
        setConditionFeedback({ type: '', message: '' });

        try {
            const response = await axios.post(`${API_BASE_URL}/generate-barcode`, {
                isbn: conditionBook.ISBN,
                id_cp_koleksi: copy.id_cp_koleksi, // ID Copy untuk barcode spesifik
                editor_nip_karyawan: getUserNip(user),
            });

            setBarcodeData(response.data);
        } catch (error) {
            console.error(error);
            setBarcodeData("<div class='text-red-500 p-4 text-center'>Gagal membuat barcode copy fisik.</div>");
        } finally {
            setIsGeneratingBarcode(false);
        }
    };

    const loadBookCopies = async (isbn) => {
        setIsLoadingCopies(true);

        try {
            const response = await axios.get(`${API_BASE_URL}/buku/${isbn}/copies`);
            setBookCopies(response.data.data?.copies || []);
            setCopyStatusOptions(response.data.data?.status_options || fallbackCopyStatusOptions);
            return response.data.data;
        } catch (error) {
            console.error(error);
            setConditionFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Gagal memuat kondisi copy buku.',
            });
        } finally {
            setIsLoadingCopies(false);
        }
    };

    const openConditionModal = async (book) => {
        setConditionBook(book);
        setShowConditionModal(true);
        setBookCopies([]);
        setCopyToDelete(null);
        setConditionFeedback({ type: '', message: '' });
        await loadBookCopies(book.ISBN);
    };

    const closeConditionModal = () => {
        setShowConditionModal(false);
        setConditionBook(null);
        setBookCopies([]);
        setSavingCopyId(null);
        setIsCreatingCopy(false);
        setCopyToDelete(null);
        setIsDeletingCopy(false);
        setConditionFeedback({ type: '', message: '' });
    };

    const handleAddCopy = async () => {
        if (!conditionBook) {
            return;
        }

        setIsCreatingCopy(true);
        setConditionFeedback({ type: '', message: '' });

        try {
            const response = await axios.post(`${API_BASE_URL}/buku/${conditionBook.ISBN}/copies`, {
                editor_nip_karyawan: getUserNip(user),
                status_buku: 'Tersedia',
            });

            setBookCopies((current) => [...current, response.data.data]);
            setConditionFeedback({ type: 'success', message: 'Copy fisik baru berhasil ditambahkan.' });
            await loadBooks();
        } catch (error) {
            console.error(error);
            setConditionFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Copy fisik gagal ditambahkan.',
            });
        } finally {
            setIsCreatingCopy(false);
        }
    };

    const handleCopyStatusChange = async (copy, nextStatus) => {
        if (!nextStatus || nextStatus === copy.status_buku) {
            return;
        }

        setSavingCopyId(copy.id_cp_koleksi);
        setConditionFeedback({ type: '', message: '' });

        try {
            const response = await axios.put(`${API_BASE_URL}/buku/copies/${copy.id_cp_koleksi}`, {
                editor_nip_karyawan: getUserNip(user),
                status_buku: nextStatus,
            });

            setBookCopies((current) => current.map((item) => (
                item.id_cp_koleksi === copy.id_cp_koleksi
                    ? { ...item, status_buku: response.data.data?.status_buku || nextStatus, sedang_dipinjam: false }
                    : item
            )));

            setConditionFeedback({ type: 'success', message: 'Kondisi copy berhasil diperbarui.' });
        } catch (error) {
            console.error(error);
            setConditionFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Kondisi copy gagal diperbarui.',
            });
        } finally {
            setSavingCopyId(null);
        }
    };

    const openCopyDeleteModal = (copy) => {
        setCopyToDelete(copy);
        setConditionFeedback({ type: '', message: '' });
    };

    const closeCopyDeleteModal = () => {
        setCopyToDelete(null);
        setIsDeletingCopy(false);
    };

    const handleDeleteCopy = async () => {
        if (!copyToDelete || !conditionBook) {
            return;
        }

        setIsDeletingCopy(true);
        setConditionFeedback({ type: '', message: '' });

        try {
            await axios.delete(`${API_BASE_URL}/buku/copies/${copyToDelete.id_cp_koleksi}`, {
                data: { editor_nip_karyawan: getUserNip(user) },
            });

            setBookCopies((current) => current.filter((item) => item.id_cp_koleksi !== copyToDelete.id_cp_koleksi));
            setConditionFeedback({ type: 'success', message: 'Copy fisik berhasil dihapus.' });
            setCopyToDelete(null);
            await loadBooks();
        } catch (error) {
            console.error(error);
            setConditionFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Copy fisik gagal dihapus.',
            });
        } finally {
            setIsDeletingCopy(false);
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

        params.set('nip', getUserNip(user));
        window.open(`${EXPORT_URL}?${params.toString()}`, '_blank', 'noopener,noreferrer');
    };

    const handleImportExcel = () => {
        const nip = encodeURIComponent(getUserNip(user));
        window.open(`${IMPORT_URL}?nip=${nip}`, '_blank', 'noopener,noreferrer');
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Manajemen Koleksi Buku</h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Pustakawan dapat menambah, mengubah, menghapus, mencari, mencetak barcode, impor, dan ekspor data buku.
                    </p>
                </div>

                <div className="flex flex-wrap gap-3">
                    <button
                        type="button"
                        onClick={handleImportExcel}
                        className="bg-white border border-[#265F9C] text-[#265F9C] px-4 py-3 rounded-xl text-sm font-bold shadow-sm hover:bg-blue-50 transition-colors"
                    >
                        Import Excel
                    </button>
                    <button
                        type="button"
                        onClick={handleExportExcel}
                        className="bg-[#2E7D32] text-white px-4 py-3 rounded-xl text-sm font-bold shadow hover:bg-[#1b5e20] transition-colors"
                    >
                        Export Excel
                    </button>
                    <button
                        type="button"
                        onClick={openCreateModal}
                        className="bg-[#265F9C] text-white px-5 py-3 rounded-xl text-sm font-bold shadow hover:bg-blue-700 transition-colors"
                    >
                        Tambah Buku
                    </button>
                </div>
            </div>

            <div className="mb-8 flex flex-wrap gap-3 justify-end">
                <input
                    type="text"
                    placeholder="Cari judul, penulis, atau ISBN..."
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
                    <option value="asc">{isYearSort ? 'Lama-Baru' : 'A-Z'}</option>
                    <option value="desc">{isYearSort ? 'Baru-Lama' : 'Z-A'}</option>
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
                            {books.map((book, index) => (
                                <tr key={book.ISBN} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                    <td className="p-4 text-center text-[#7D7D7E]">{(bookPage - 1) * 10 + index + 1}</td>
                                    <td className="p-4">
                                        <p className="font-bold text-[#1A1A1A]">{book.judul_koleksi}</p>
                                        <p className="text-[11px] text-[#7D7D7E] font-mono mt-1">{formatIsbn(book.ISBN)}</p>
                                    </td>
                                    <td className="p-4 text-[#585858]">{book.pengarang}</td>
                                    <td className="p-4 text-[#585858]">{book.kategori}</td>
                                    <td className="p-4 text-[#585858]">{book.no_rak_buku || '-'}</td>
                                    <td className="p-4 text-center">
                                        <span className="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{book.jumlah_eksemplar}</span>
                                    </td>
                                    <td className="p-4 text-center">
                                        <div className="flex justify-center gap-2 flex-wrap">
                                            <button
                                                type="button"
                                                onClick={() => openEditModal(book)}
                                                className="bg-amber-500 text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-amber-600 transition-colors"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => openDeleteModal(book)}
                                                className="bg-[#C62828] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-red-700 transition-colors"
                                            >
                                                Hapus
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => handleGenerateBarcode(book)}
                                                className="bg-[#265F9C] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-blue-700 transition-colors"
                                            >
                                                Barcode
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => openConditionModal(book)}
                                                className="bg-slate-700 text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-slate-800 transition-colors"
                                            >
                                                Kondisi
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

            {showFormModal && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-3xl relative">
                        <button type="button" onClick={closeFormModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-1 text-[#265F9C]">
                            {formMode === 'create' ? 'Tambah Koleksi Buku' : 'Ubah Koleksi Buku'}
                        </h2>
                        <p className="text-sm text-[#585858] mb-6">
                            Validasi ISBN, kategori, dan jumlah copy tetap berlaku sebelum data disimpan.
                        </p>

                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">ISBN<RequiredMark /></label>
                                    <input
                                        name="ISBN"
                                        value={formData.ISBN}
                                        onChange={handleFormChange}
                                        disabled={formMode === 'edit'}
                                        inputMode="numeric"
                                        maxLength={17}
                                        placeholder="978-602-8519-93-9"
                                        className={`w-full rounded-xl border p-3 text-sm outline-none ${formMode === 'edit' ? 'bg-gray-100 text-[#585858] font-mono' : 'focus:ring-2 focus:ring-[#265F9C]'}`}
                                    />
                                    <p className="mt-1 text-[11px] text-[#7D7D7E]">ISBN akan otomatis ditampilkan dengan tanda strip.</p>
                                    {formErrors.ISBN && <p className="mt-1 text-xs text-red-600">{formErrors.ISBN[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Kategori<RequiredMark /></label>
                                    <select name="id_ref_koleksi" value={formData.id_ref_koleksi} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]">
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
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Judul Buku<RequiredMark /></label>
                                    <input name="judul_koleksi" value={formData.judul_koleksi} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.judul_koleksi && <p className="mt-1 text-xs text-red-600">{formErrors.judul_koleksi[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Penulis<RequiredMark /></label>
                                    <input name="pengarang" value={formData.pengarang} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.pengarang && <p className="mt-1 text-xs text-red-600">{formErrors.pengarang[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Penerbit<RequiredMark /></label>
                                    <input name="penerbit" value={formData.penerbit} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.penerbit && <p className="mt-1 text-xs text-red-600">{formErrors.penerbit[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Tahun<RequiredMark /></label>
                                    <input name="tahun" value={formData.tahun} onChange={handleFormChange} maxLength={4} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.tahun && <p className="mt-1 text-xs text-red-600">{formErrors.tahun[0]}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Jumlah Eksemplar<RequiredMark /></label>
                                    <input name="jumlah_eksemplar" type="number" min="1" value={formData.jumlah_eksemplar} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.jumlah_eksemplar && <p className="mt-1 text-xs text-red-600">{formErrors.jumlah_eksemplar[0]}</p>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Nomor Rak<RequiredMark /></label>
                                    <input name="no_rak_buku" value={formData.no_rak_buku} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.no_rak_buku && <p className="mt-1 text-xs text-red-600">{formErrors.no_rak_buku[0]}</p>}
                                </div>
                                <div className="md:col-span-2">
                                    <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Keterangan</label>
                                    <textarea name="keterangan_buku" value={formData.keterangan_buku} onChange={handleFormChange} rows="3" className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                    {formErrors.keterangan_buku && <p className="mt-1 text-xs text-red-600">{formErrors.keterangan_buku[0]}</p>}
                                </div>
                            </div>

                            <div className="flex justify-end gap-3">
                                <button type="button" onClick={closeFormModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" disabled={isSaving} className="px-5 py-2 bg-[#265F9C] text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition-all disabled:opacity-50">
                                    {isSaving ? 'Menyimpan...' : formMode === 'create' ? 'Simpan Buku' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showDeleteModal && selectedBook && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg relative">
                        <button type="button" onClick={closeDeleteModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-2 text-[#C62828]">Hapus Koleksi Buku</h2>
                        <p className="text-sm text-[#585858] leading-6">
                            Anda akan menghapus koleksi <span className="font-bold text-[#1A1A1A]">{selectedBook.judul_koleksi}</span>.
                            Tindakan ini memerlukan konfirmasi pustakawan dan akan ditolak bila masih ada peminjaman aktif.
                        </p>
                        <div className="mt-4 rounded-xl bg-gray-50 border p-4 text-sm">
                            <p><span className="font-bold">ISBN:</span> {formatIsbn(selectedBook.ISBN)}</p>
                            <p><span className="font-bold">Penulis:</span> {selectedBook.pengarang}</p>
                            <p><span className="font-bold">Kategori:</span> {selectedBook.kategori}</p>
                        </div>
                        <div className="mt-6 flex justify-end gap-3">
                            <button type="button" onClick={closeDeleteModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="button" onClick={handleDeleteBook} disabled={isDeleting} className="px-5 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all disabled:opacity-50">
                                {isDeleting ? 'Menghapus...' : 'Ya, Hapus Buku'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {showConditionModal && conditionBook && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-4xl relative">
                        <button type="button" onClick={closeConditionModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <div className="mb-5 flex flex-col gap-3 pr-8 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 className="text-xl font-bold font-montserrat mb-1 text-[#265F9C]">Kelola Copy Fisik Buku</h2>
                                <p className="text-sm text-[#585858]">
                                    {conditionBook.judul_koleksi} <span className="font-mono text-xs">({formatIsbn(conditionBook.ISBN)})</span>
                                </p>
                            </div>
                            <button
                                type="button"
                                onClick={handleAddCopy}
                                disabled={isCreatingCopy || isLoadingCopies}
                                className="rounded-xl bg-[#265F9C] px-4 py-2 text-xs font-bold text-white shadow hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                                {isCreatingCopy ? 'Menambah...' : '+ Tambah Copy'}
                            </button>
                        </div>

                        {conditionFeedback.message && (
                            <div className={`mb-4 rounded-xl border px-4 py-3 text-sm font-medium ${conditionFeedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'}`}>
                                {conditionFeedback.message}
                            </div>
                        )}

                        {isLoadingCopies ? (
                            <div className="text-center py-10 text-gray-500">Memuat kondisi copy...</div>
                        ) : (
                            <div className="overflow-x-auto border rounded-xl">
                                <table className="w-full text-left">
                                    <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                                        <tr>
                                            <th className="p-4">ID Copy</th>
                                            <th className="p-4">Status Saat Ini</th>
                                            <th className="p-4">Ubah Kondisi</th>
                                            <th className="p-4">Catatan</th>
                                            <th className="p-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {bookCopies.length === 0 ? (
                                            <tr>
                                                <td colSpan="5" className="p-8 text-center text-sm text-gray-500">Belum ada copy fisik untuk buku ini.</td>
                                            </tr>
                                        ) : (
                                            bookCopies.map((copy) => {
                                                const lowerStatus = String(copy.status_buku || '').toLowerCase();
                                                const locked = copy.sedang_dipinjam || lowerStatus === 'dimusnahkan';
                                                const canDelete = !locked && bookCopies.length > 1;

                                                return (
                                                    <tr key={copy.id_cp_koleksi} className="border-b last:border-b-0 text-sm">
                                                        <td className="p-4 font-mono font-bold text-[#265F9C]">#{copy.id_cp_koleksi}</td>
                                                        <td className="p-4">
                                                            <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-bold ${statusBadgeClass(copy.status_buku)}`}>
                                                                {copy.status_buku || 'Tersedia'}
                                                            </span>
                                                        </td>
                                                        <td className="p-4">
                                                            <select
                                                                value={copy.status_buku || 'Tersedia'}
                                                                disabled={locked || savingCopyId === copy.id_cp_koleksi}
                                                                onChange={(event) => handleCopyStatusChange(copy, event.target.value)}
                                                                className="w-full min-w-[160px] rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C] disabled:bg-gray-100 disabled:text-gray-500"
                                                            >
                                                                {copyStatusOptions.map((status) => (
                                                                    <option key={status} value={status}>{status}</option>
                                                                ))}
                                                                {!copyStatusOptions.includes(copy.status_buku) && copy.status_buku && (
                                                                    <option value={copy.status_buku}>{copy.status_buku}</option>
                                                                )}
                                                            </select>
                                                        </td>
                                                        <td className="p-4 text-xs text-[#7D7D7E]">
                                                            {copy.sedang_dipinjam
                                                                ? 'Sedang dipinjam, ubah melalui proses pengembalian.'
                                                                : String(copy.status_buku || '').toLowerCase() === 'dimusnahkan'
                                                                    ? 'Sudah dimusnahkan.'
                                                                    : savingCopyId === copy.id_cp_koleksi
                                                                        ? 'Menyimpan...'
                                                                        : '-'}
                                                        </td>
                                                        <td className="p-4 text-right">
                                                            <div className="flex justify-end gap-2 flex-wrap">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleGenerateCopyBarcode(copy)}
                                                                    className="rounded-lg border border-[#265F9C] bg-blue-50 px-3 py-2 text-[11px] font-bold text-[#265F9C] hover:bg-blue-100 transition-colors"
                                                                >
                                                                    Barcode
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => openCopyDeleteModal(copy)}
                                                                    disabled={!canDelete || isDeletingCopy}
                                                                    title={!canDelete ? 'Copy sedang terkunci atau merupakan copy terakhir.' : 'Hapus copy fisik'}
                                                                    className="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[11px] font-bold text-[#C62828] hover:bg-red-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:bg-gray-100 disabled:text-gray-400"
                                                                >
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        )}

                        <div className="mt-6 flex justify-end">
                            <button type="button" onClick={closeConditionModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {copyToDelete && conditionBook && (
                <div className="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <div className="mb-4">
                            <p className="text-xs font-bold uppercase tracking-[0.18em] text-[#7D7D7E]">Konfirmasi</p>
                            <h3 className="mt-2 text-lg font-bold text-[#1A1A1A]">Hapus copy fisik?</h3>
                        </div>
                        <p className="text-sm leading-relaxed text-[#585858]">
                            Copy <span className="font-mono font-bold text-[#265F9C]">#{copyToDelete.id_cp_koleksi}</span> dari buku <span className="font-bold text-[#1A1A1A]">{conditionBook.judul_koleksi}</span> akan dihapus dari data master. Sistem akan menolak bila copy sudah memiliki riwayat peminjaman.
                        </p>
                        <div className="mt-6 flex justify-end gap-3">
                            <button type="button" onClick={closeCopyDeleteModal} disabled={isDeletingCopy} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors disabled:opacity-50">
                                Batal
                            </button>
                            <button type="button" onClick={handleDeleteCopy} disabled={isDeletingCopy} className="px-5 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all disabled:opacity-50">
                                {isDeletingCopy ? 'Menghapus...' : 'Ya, Hapus'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {showBarcodeModal && (
                <div className="fixed inset-0 bg-black/60 z-[70] flex items-center justify-center p-4 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-2xl relative animate-fade-in-up">
                        <button type="button" onClick={() => setShowBarcodeModal(false)} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-4 text-[#265F9C] border-b pb-2">Cetak Barcode</h2>
                        <div className="flex flex-col items-center justify-center min-h-[200px] bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-4 mb-6">
                            {isGeneratingBarcode ? (
                                <div className="text-gray-500 flex flex-col items-center">
                                    <span className="text-4xl animate-bounce mb-2">...</span>
                                    <p className="text-sm font-medium animate-pulse">Membuat barcode...</p>
                                </div>
                            ) : (
                                <div dangerouslySetInnerHTML={{ __html: barcodeData }} />
                            )}
                        </div>
                        <div className="flex justify-end gap-3">
                            <button type="button" onClick={() => setShowBarcodeModal(false)} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                Tutup
                            </button>
                            <button type="button" onClick={() => window.print()} disabled={isGeneratingBarcode} className="px-5 py-2 bg-[#2E7D32] text-white rounded-xl text-sm font-bold hover:bg-[#1b5e20] shadow-md transition-all disabled:opacity-50">
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