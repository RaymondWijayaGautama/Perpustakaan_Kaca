import { useState, useEffect } from 'react';
import axios from 'axios';
import { 
  PlusIcon, 
  PencilSquareIcon, 
  TrashIcon, 
  XMarkIcon 
} from '@heroicons/react/24/outline';
import useConfirmDialog from './useConfirmDialog';

const KategoriPanel = () => {
  const { confirm, ConfirmDialog } = useConfirmDialog();
  const [categories, setCategories] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [modalState, setModalState] = useState({ isOpen: false, type: 'add' }); 
  const [formData, setFormData] = useState({
    ID_REF_KOLEKSI: '',
    NO_KATEGORI_BUKU: '',
    DESKRIPSI_KATEGORI: ''
  });
  const [message, setMessage] = useState({ type: '', text: '' });

  const API_URL = 'http://localhost:8000/api/kategori'; 

  const getAuthHeader = () => ({
    headers: { 
      'Authorization': `Bearer ${localStorage.getItem('token')}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  });

  const fetchCategories = async () => {
    setIsLoading(true);
    try {
      const response = await axios.get(API_URL, getAuthHeader());
      const result = response.data.data || response.data;
      setCategories(Array.isArray(result) ? result : []);
    } catch (error) {
      showMessage('error', 'Gagal memuat data kategori.');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchCategories();
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    // Payload kembali seperti semula, tanpa editor_nip_karyawan
    const payload = {
      NO_KATEGORI_BUKU: formData.NO_KATEGORI_BUKU,
      DESKRIPSI_KATEGORI: formData.DESKRIPSI_KATEGORI
    };

    try {
      if (modalState.type === 'add') {
        await axios.post(API_URL, payload, getAuthHeader());
        showMessage('success', 'Kategori berhasil ditambahkan.');
      } else {
        await axios.put(`${API_URL}/${formData.ID_REF_KOLEKSI}`, payload, getAuthHeader());
        showMessage('success', 'Kategori berhasil diperbarui.');
      }
      closeModal();
      fetchCategories();
    } catch (error) {
      let errorMsg = 'Gagal menyimpan data kategori.';
      if (error.response && error.response.status === 422) {
         const validationErrors = error.response.data.errors;
         if (validationErrors) {
             errorMsg = Object.values(validationErrors)[0][0]; 
         } else if (error.response.data.message) {
             errorMsg = error.response.data.message;
         }
      }
      showMessage('error', errorMsg);
    }
  };

  const handleDelete = async (id) => {
    const approved = await confirm({
      title: 'Hapus Kategori',
      message: 'Apakah Anda yakin ingin menghapus kategori ini?',
      confirmLabel: 'Ya, Hapus',
      tone: 'danger',
    });

    if (!approved) return;

    try {
      await axios.delete(`${API_URL}/${id}`, getAuthHeader());
      showMessage('success', 'Kategori berhasil dihapus.');
      fetchCategories();
    } catch (error) {
      showMessage('error', 'Gagal menghapus kategori.');
    }
  };

  const openModal = (type, category = null) => {
    setModalState({ isOpen: true, type });
    if (category) {
      setFormData({
        ID_REF_KOLEKSI: category.ID_REF_KOLEKSI,
        NO_KATEGORI_BUKU: category.NO_KATEGORI_BUKU || '',
        DESKRIPSI_KATEGORI: category.DESKRIPSI_KATEGORI || ''
      });
    } else {
      setFormData({ ID_REF_KOLEKSI: '', NO_KATEGORI_BUKU: '', DESKRIPSI_KATEGORI: '' });
    }
  };

  const closeModal = () => {
    setModalState({ isOpen: false, type: 'add' });
    setFormData({ ID_REF_KOLEKSI: '', NO_KATEGORI_BUKU: '', DESKRIPSI_KATEGORI: '' });
  };

  const showMessage = (type, text) => {
    setMessage({ type, text });
    setTimeout(() => setMessage({ type: '', text: '' }), 4000);
  };

  return (
    <div className="min-h-screen bg-[#F6F7F9] p-6">
      
      <div className="max-w-6xl mx-auto">
        
        <div className="flex justify-between items-center mb-6">
          <div>
            <h1 className="text-[28px] font-montserrat font-semibold text-[#1A1A1A] leading-[36px]">
              Kategori Koleksi
            </h1>
            <p className="text-[14px] font-roboto text-[#585858] mt-1">
              Kelola data referensi kategori buku perpustakaan.
            </p>
          </div>
          
          <button 
            onClick={() => openModal('add')}
            className="flex items-center gap-2 bg-[#265F9C] text-white px-4 py-2 rounded-[8px] font-roboto font-medium hover:opacity-90 transition-opacity duration-150 shadow-[0_1px_3px_rgba(0,0,0,0.1)]"
          >
            <PlusIcon className="w-6 h-6 stroke-[1.5px]" />
            <span>Tambah Kategori</span>
          </button>
        </div>

        {message.text && (
          <div className={`mb-4 p-4 rounded-[8px] font-roboto font-medium flex items-center ${
            message.type === 'success' ? 'bg-[#2E7D32]/10 text-[#2E7D32]' : 'bg-[#C62828]/10 text-[#C62828]'
          }`}>
            {message.text}
          </div>
        )}

        <div className="bg-white rounded-[8px] shadow-[0_4px_8px_rgba(0,0,0,0.15)] overflow-hidden">
          <table className="w-full text-left border-collapse">
            <thead className="bg-[#F6F7F9] border-b border-gray-200">
              <tr>
                <th className="px-6 py-4 font-montserrat font-semibold text-[14px] text-[#585858]">No. Kategori</th>
                <th className="px-6 py-4 font-montserrat font-semibold text-[14px] text-[#585858]">Deskripsi Kategori</th>
                <th className="px-6 py-4 font-montserrat font-semibold text-[14px] text-[#585858] text-center w-48">Aksi</th>
              </tr>
            </thead>
            <tbody className="font-roboto text-[16px] text-[#1A1A1A]">
              {isLoading ? (
                <tr>
                  <td colSpan="3" className="text-center py-8 text-[#585858]">Memuat data...</td>
                </tr>
              ) : categories.length === 0 ? (
                <tr>
                  <td colSpan="3" className="text-center py-8 text-[#585858]">Tidak ada data kategori.</td>
                </tr>
              ) : (
                categories.map((item) => (
                  <tr key={item.ID_REF_KOLEKSI} className="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                    <td className="px-6 py-4">{item.NO_KATEGORI_BUKU || '-'}</td>
                    <td className="px-6 py-4">{item.DESKRIPSI_KATEGORI}</td>
                    <td className="px-6 py-4 flex items-center justify-center gap-4">
                      
                      <button 
                        onClick={() => openModal('edit', item)}
                        className="flex items-center gap-1 text-[#265F9C] hover:text-[#1d4775] transition-colors"
                        title="Edit Data"
                      >
                        <PencilSquareIcon className="w-5 h-5 stroke-[1.5px]" />
                        <span className="text-[14px]">Edit</span>
                      </button>

                      <button 
                        onClick={() => handleDelete(item.ID_REF_KOLEKSI)}
                        className="flex items-center gap-1 text-[#C62828] hover:text-[#9e2020] transition-colors"
                        title="Hapus Data"
                      >
                        <TrashIcon className="w-5 h-5 stroke-[1.5px]" />
                        <span className="text-[14px]">Hapus</span>
                      </button>

                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

      </div>

      {modalState.isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
          <div className="bg-white rounded-[8px] shadow-[0_4px_8px_rgba(0,0,0,0.15)] w-full max-w-lg overflow-hidden">
            
            <div className="flex justify-between items-center px-6 py-4 border-b border-gray-200">
              <h3 className="font-montserrat text-[22px] font-semibold text-[#1A1A1A]">
                {modalState.type === 'add' ? 'Tambah Kategori' : 'Edit Kategori'}
              </h3>
              <button onClick={closeModal} className="text-[#585858] hover:text-[#C62828] transition-colors">
                <XMarkIcon className="w-6 h-6 stroke-[1.5px]" />
              </button>
            </div>

            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block font-roboto text-[14px] text-[#585858] mb-1">
                  Nomor Kategori (Opsional)
                </label>
                <input 
                  type="text" 
                  value={formData.NO_KATEGORI_BUKU || ''}
                  onChange={(e) => setFormData({...formData, NO_KATEGORI_BUKU: e.target.value})}
                  className="w-full border border-gray-300 rounded-[4px] px-3 py-2 font-roboto text-[#1A1A1A] focus:outline-none focus:border-[#265F9C] focus:ring-1 focus:ring-[#265F9C]"
                  placeholder="Misal: 000"
                />
              </div>
              
              <div>
                <label className="block font-roboto text-[14px] text-[#585858] mb-1">
                  Deskripsi Kategori <span className="text-[#C62828]">*</span>
                </label>
                <textarea 
                  required
                  rows="4"
                  value={formData.DESKRIPSI_KATEGORI}
                  onChange={(e) => setFormData({...formData, DESKRIPSI_KATEGORI: e.target.value})}
                  className="w-full border border-gray-300 rounded-[4px] px-3 py-2 font-roboto text-[#1A1A1A] focus:outline-none focus:border-[#265F9C] focus:ring-1 focus:ring-[#265F9C] resize-none"
                  placeholder="Misal: Karya Umum (Buku yang mencakup pengetahuan dasar dari berbagai bidang...)"
                ></textarea>
              </div>

              <div className="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                <button 
                  type="button" 
                  onClick={closeModal}
                  className="px-4 py-2 rounded-[4px] font-roboto font-medium text-[#585858] hover:bg-gray-100 transition-colors"
                >
                  Batal
                </button>
                <button 
                  type="submit"
                  className="px-4 py-2 rounded-[4px] font-roboto font-medium bg-[#265F9C] text-white hover:bg-opacity-90 transition-opacity"
                >
                  Simpan Data
                </button>
              </div>
            </form>
            
          </div>
        </div>
      )}
      <ConfirmDialog />
      
    </div>
  );
};

export default KategoriPanel;
