import React, { useDeferredValue, useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const emptyForm = {
    kode_kategori: '',
    deskripsi_kategori: '',
};

const RequiredMark = () => <span className="text-red-500 ml-1">*</span>;

const ManajemenKoleksiPanel = ({ user }) => {
    const [collections, setCollections] = useState([]);
    const [search, setSearch] = useState('');
    const [sortBy, setSortBy] = useState('deskripsi');
    const [sortOrder, setSortOrder] = useState('asc');
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({});
    const [loading, setLoading] = useState(false);
    const [feedback, setFeedback] = useState({ type: '', message: '' });

    const [showFormModal, setShowFormModal] = useState(false);
    const [formMode, setFormMode] = useState('create');
    const [formData, setFormData] = useState(emptyForm);
    const [formErrors, setFormErrors] = useState({});
    const [isSaving, setIsSaving] = useState(false);
    const [selectedCollection, setSelectedCollection] = useState(null);

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const deferredSearch = useDeferredValue(search);

    const loadCollections = async ({
        currentSearch = deferredSearch,
        currentSortBy = sortBy,
        currentSortOrder = sortOrder,
        currentPage = page,
    } = {}) => {
        setLoading(true);

        try {
            const res = await axios.get(`${API_BASE_URL}/koleksi`, {
                params: {
                    search: currentSearch,
                    sort_by: currentSortBy,
                    sort_order: currentSortOrder,
                    page: currentPage,
                    per_page: 10,
                },
            });

            setCollections(res.data.data || []);
            setPagination(res.data || {});
        } catch (error) {
            console.error(error);
            setFeedback({ type: 'error', message: 'Gagal memuat master koleksi.' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadCollections();
    }, [deferredSearch, sortBy, sortOrder, page]);

    const closeFormModal = () => {
        setShowFormModal(false);
        setSelectedCollection(null);
        setFormData(emptyForm);
        setFormErrors({});
        setIsSaving(false);
    };

    const openCreateModal = () => {
        setFeedback({ type: '', message: '' });
        setFormMode('create');
        setFormData(emptyForm);
        setFormErrors({});
        setSelectedCollection(null);
        setShowFormModal(true);
    };

    const openEditModal = (collection) => {
        setFeedback({ type: '', message: '' });
        setFormMode('edit');
        setSelectedCollection(collection);
        setFormData({
            kode_kategori: collection.kode_kategori ?? '',
            deskripsi_kategori: collection.deskripsi_kategori ?? '',
        });
        setFormErrors({});
        setShowFormModal(true);
    };

    const handleFormChange = (event) => {
        const { name, value } = event.target;
        setFormData((current) => ({ ...current, [name]: value }));
        setFormErrors((current) => ({ ...current, [name]: undefined }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setIsSaving(true);
        setFormErrors({});
        setFeedback({ type: '', message: '' });

        const payload = {
            editor_nip_karyawan: user?.nip_karyawan,
            kode_kategori: formData.kode_kategori.trim(),
            deskripsi_kategori: formData.deskripsi_kategori.trim(),
        };

        try {
            if (formMode === 'create') {
                await axios.post(`${API_BASE_URL}/koleksi`, payload);
                setFeedback({ type: 'success', message: 'Master koleksi berhasil ditambahkan.' });
            } else {
                await axios.put(`${API_BASE_URL}/koleksi/${selectedCollection.id_ref_koleksi}`, payload);
                setFeedback({ type: 'success', message: 'Master koleksi berhasil diperbarui.' });
            }

            closeFormModal();
            setPage(1);
            loadCollections({ currentPage: 1 });
        } catch (error) {
            console.error(error);

            if (error.response?.status === 422) {
                setFormErrors(error.response.data.errors || {});
            }

            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Master koleksi gagal disimpan.',
            });
        } finally {
            setIsSaving(false);
        }
    };

    const openDeleteModal = (collection) => {
        setSelectedCollection(collection);
        setShowDeleteModal(true);
        setFeedback({ type: '', message: '' });
    };

    const closeDeleteModal = () => {
        setSelectedCollection(null);
        setShowDeleteModal(false);
        setIsDeleting(false);
    };

    const handleDelete = async () => {
        if (!selectedCollection) {
            return;
        }

        setIsDeleting(true);
        setFeedback({ type: '', message: '' });

        try {
            await axios.delete(`${API_BASE_URL}/koleksi/${selectedCollection.id_ref_koleksi}`, {
                data: {
                    editor_nip_karyawan: user?.nip_karyawan,
                },
            });

            closeDeleteModal();
            setFeedback({ type: 'success', message: 'Master koleksi berhasil dihapus.' });

            if (collections.length === 1 && page > 1) {
                setPage(page - 1);
            } else {
                loadCollections();
            }
        } catch (error) {
            console.error(error);
            closeDeleteModal();
            setFeedback({
                type: 'error',
                message: error.response?.data?.message || 'Master koleksi gagal dihapus.',
            });
        } finally {
            setIsDeleting(false);
        }
    };

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
                <div>
                    <h1 className="text-2xl font-bold font-montserrat">Manajemen Master Koleksi</h1>
                    <p className="text-sm text-[#585858] mt-1">
                        Kelola kategori koleksi aktif yang menjadi referensi data buku.
                    </p>
                </div>
                <button
                    type="button"
                    onClick={openCreateModal}
                    className="bg-[#265F9C] text-white px-5 py-3 rounded-xl text-sm font-bold shadow hover:bg-blue-700 transition-colors"
                >
                    Tambah Master Koleksi
                </button>
            </div>

            <div className="mb-8 flex flex-wrap gap-3 justify-end">
                <input
                    type="text"
                    placeholder="Cari ID, kode, atau deskripsi..."
                    className="p-3 border rounded-xl text-sm outline-none min-w-[260px] focus:ring-2 focus:ring-[#265F9C] transition-all shadow-sm"
                    value={search}
                    onChange={(event) => {
                        setSearch(event.target.value);
                        setPage(1);
                    }}
                />
                <select
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
                    value={sortBy}
                    onChange={(event) => {
                        setSortBy(event.target.value);
                        setPage(1);
                    }}
                >
                    <option value="deskripsi">Urut: Kategori</option>
                    <option value="kode">Urut: Kode</option>
                    <option value="id_ref_koleksi">Urut: ID</option>
                </select>
                <select
                    className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
                    value={sortOrder}
                    onChange={(event) => {
                        setSortOrder(event.target.value);
                        setPage(1);
                    }}
                >
                    <option value="asc">A-Z / Kecil-Besar</option>
                    <option value="desc">Z-A / Besar-Kecil</option>
                </select>
            </div>

            {feedback.message && (
                <div className={`mb-6 rounded-xl border px-4 py-3 text-sm font-medium ${feedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'}`}>
                    {feedback.message}
                </div>
            )}

            {loading ? (
                <div className="text-center py-10 text-gray-500">Memuat master koleksi...</div>
            ) : (
                <>
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
                            <tr>
                                <th className="p-4 w-16 text-center">ID</th>
                                <th className="p-4">Kode</th>
                                <th className="p-4">Kategori</th>
                                <th className="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="font-roboto">
                            {collections.map((collection) => (
                                <tr key={collection.id_ref_koleksi} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                    <td className="p-4 text-center text-[#7D7D7E]">{collection.id_ref_koleksi}</td>
                                    <td className="p-4 font-mono font-bold text-[#1A1A1A]">{collection.kode_kategori}</td>
                                    <td className="p-4 text-[#585858]">{collection.deskripsi_kategori}</td>
                                    <td className="p-4 text-center">
                                        <div className="flex justify-center gap-2">
                                            <button type="button" onClick={() => openEditModal(collection)} className="bg-amber-500 text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-amber-600 transition-colors">
                                                Edit
                                            </button>
                                            <button type="button" onClick={() => openDeleteModal(collection)} className="bg-[#C62828] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-red-700 transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                        <button disabled={page === 1} onClick={() => setPage(page - 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">
                            Prev
                        </button>
                        <span className="text-xs font-bold text-[#585858] uppercase">
                            Halaman {page} / {pagination.last_page || 1}
                        </span>
                        <button disabled={page === pagination.last_page || !pagination.last_page} onClick={() => setPage(page + 1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">
                            Next
                        </button>
                    </div>
                </>
            )}

            {showFormModal && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-xl relative">
                        <button type="button" onClick={closeFormModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-1 text-[#265F9C]">
                            {formMode === 'create' ? 'Tambah Master Koleksi' : 'Ubah Master Koleksi'}
                        </h2>
                        <p className="text-sm text-[#585858] mb-6">
                            Kode kategori harus unik dan hanya pustakawan yang dapat menyimpan perubahan.
                        </p>

                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Kode Kategori<RequiredMark /></label>
                                <input name="kode_kategori" value={formData.kode_kategori} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                {formErrors.kode_kategori && <p className="mt-1 text-xs text-red-600">{formErrors.kode_kategori[0]}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-bold uppercase text-[#585858] mb-2">Kategori<RequiredMark /></label>
                                <input name="deskripsi_kategori" value={formData.deskripsi_kategori} onChange={handleFormChange} className="w-full rounded-xl border p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]" />
                                {formErrors.deskripsi_kategori && <p className="mt-1 text-xs text-red-600">{formErrors.deskripsi_kategori[0]}</p>}
                            </div>
                            <div className="flex justify-end gap-3">
                                <button type="button" onClick={closeFormModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" disabled={isSaving} className="px-5 py-2 bg-[#265F9C] text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition-all disabled:opacity-50">
                                    {isSaving ? 'Menyimpan...' : formMode === 'create' ? 'Simpan Master' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showDeleteModal && selectedCollection && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg relative">
                        <button type="button" onClick={closeDeleteModal} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-2 text-[#C62828]">Hapus Master Koleksi</h2>
                        <p className="text-sm text-[#585858] leading-6">
                            Anda akan menghapus kategori <span className="font-bold text-[#1A1A1A]">{selectedCollection.deskripsi_kategori}</span>.
                            Sistem akan menolak penghapusan bila kategori masih dipakai data buku aktif.
                        </p>
                        <div className="mt-4 rounded-xl bg-gray-50 border p-4 text-sm">
                            <p><span className="font-bold">ID:</span> {selectedCollection.id_ref_koleksi}</p>
                            <p><span className="font-bold">Kode:</span> {selectedCollection.kode_kategori}</p>
                        </div>
                        <div className="mt-6 flex justify-end gap-3">
                            <button type="button" onClick={closeDeleteModal} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="button" onClick={handleDelete} disabled={isDeleting} className="px-5 py-2 bg-[#C62828] text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-md transition-all disabled:opacity-50">
                                {isDeleting ? 'Menghapus...' : 'Ya, Hapus Master'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ManajemenKoleksiPanel;
