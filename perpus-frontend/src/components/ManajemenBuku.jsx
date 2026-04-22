/* eslint-disable react-hooks/exhaustive-deps */
import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';

const ManajemenBuku = () => {
    const [books, setBooks] = useState([]);
    const [bookSearch, setBookSearch] = useState('');
    const [bookSortBy, setBookSortBy] = useState('judul_koleksi');
    const [bookPage, setBookPage] = useState(1);
    const [bookPagination, setBookPagination] = useState({});
    const [loading, setLoading] = useState(false);
    const [showModal, setShowModal] = useState(false);
    const [barcodeData, setBarcodeData] = useState(null);
    const [isGenerating, setIsGenerating] = useState(false);

    const fileInputRef = useRef(null);
    const [isImporting, setIsImporting] = useState(false);
    // -----------------------------

    const fetchBooks = async () => {
        setLoading(true);
        try {
            const res = await axios.get('http://localhost:8000/api/buku', {
                params: { search: bookSearch, sort_by: bookSortBy, page: bookPage, per_page: 10 }
            });
            setBooks(res.data.data);
            setBookPagination(res.data);
        } catch (error) { 
            console.error(error); 
        } finally { 
            setLoading(false); 
        }
    };

    useEffect(() => {
        fetchBooks();
    }, [bookPage, bookSortBy]);

    const handleGenerateBarcode = async (buku) => {
        const identifier = buku.ISBN || buku.isbn || buku.id_mst_koleksi; 
        if (!identifier) {
            alert("Waduh, data ISBN untuk buku ini tidak ditemukan!");
            console.log("Data buku bermasalah:", buku); 
            return; 
        }

        setShowModal(true);      
        setIsGenerating(true);   
        setBarcodeData(null);    

        try {
            const response = await axios.post('http://localhost:8000/api/generate-barcode', {
                isbn: identifier 
            });
            
            setBarcodeData(response.data);
        } catch (error) {
            console.error(error);
            setBarcodeData("<div class='text-red-500 p-4 text-center'>Gagal memuat barcode. Cek koneksi API.</div>");
        } finally {
            setIsGenerating(false); 
        }
    };

    const handleImportClick = () => {
        fileInputRef.current.click();
    };

    const handleFileChange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file_excel', file);

        setIsImporting(true);
        try {
            const res = await axios.post('http://localhost:8000/api/buku/import', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            alert('Berhasil: ' + res.data.message);
            fetchBooks(); // Refresh tabel setelah sukses import
        } catch (err) {
            console.error("Error import:", err);
            alert('Gagal: ' + (err.response?.data?.message || err.message));
        } finally {
            setIsImporting(false);
            e.target.value = null; 
        }
    };
    // ------------------------------------

    return (
        <div className="bg-white rounded-xl shadow p-6 border border-gray-100">
            <div className="flex justify-between items-center mb-8">
                <h1 className="text-2xl font-bold font-montserrat">Manajemen Buku</h1>
                <div className="flex gap-3 flex-1 justify-end items-center">
                    
                    {/* --- TAMBAHAN INPUT & TOMBOL IMPORT/EXPORT --- */}
                    <input 
                        type="file" 
                        ref={fileInputRef} 
                        onChange={handleFileChange} 
                        accept=".xlsx, .xls, .csv" 
                        className="hidden" 
                    />
                    <button 
                        onClick={handleImportClick}
                        disabled={isImporting}
                        className={`px-4 py-3 rounded-xl text-xs font-bold text-white transition-all shadow-sm flex items-center gap-2 ${
                            isImporting ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#265F9C] hover:bg-blue-700'
                        }`}
                    >
                        {isImporting ? '⏳ Mengimpor...' : '📥 Import'}
                    </button>
                    <button 
                        onClick={() => window.open('http://localhost:8000/pustakawan/buku/export', '_blank')}
                        className="px-4 py-3 bg-[#2D7A4D] hover:bg-green-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2"
                    >
                        📤 Export
                    </button>
                    {/* ------------------------------------------- */}

                    <input 
                        type="text" placeholder="Cari Judul atau Penulis..." 
                        className="p-3 border rounded-xl text-sm outline-none w-2/3 focus:ring-2 focus:ring-[#265F9C] transition-all shadow-sm"
                        value={bookSearch} 
                        onChange={(e) => setBookSearch(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && fetchBooks()}
                    />
                    <select 
                        className="p-3 border rounded-xl text-sm bg-gray-50 font-medium" 
                        value={bookSortBy} 
                        onChange={(e) => setBookSortBy(e.target.value)}
                    >
                        <option value="judul_koleksi">Urut: Judul</option>
                        <option value="pengarang">Urut: Penulis</option>
                        <option value="tahun">Urut: Tahun</option>
                    </select>
                </div>
            </div>

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
                                <th className="p-4">Keterangan</th>
                                <th className="p-4 text-center">Copy</th>
                                <th className="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="font-roboto">
                            {books.map((b, i) => (
                                <tr key={i} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                                    <td className="p-4 text-center text-[#7D7D7E]">{(bookPage - 1) * 10 + i + 1}</td>
                                    <td className="p-4 font-bold text-[#1A1A1A]">{b.judul_koleksi}</td>
                                    <td className="p-4 text-[#585858]">{b.pengarang}</td>
                                    <td className="p-4 text-[#585858] italic text-xs max-w-xs truncate">{b.keterangan_buku || '-'}</td>
                                    <td className="p-4 text-center"><span className="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{b.jumlah_ekslempar}</span></td>
                                    <td className="p-4 text-center">
                                    <button 
                                            onClick={() => handleGenerateBarcode(b)} 
                                            className="bg-[#265F9C] text-white text-[10px] px-3 py-1.5 rounded shadow hover:bg-blue-700 transition-colors"
                                        >
                                            Cetak Barcode
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    
                    <div className="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                        <button disabled={bookPage === 1} onClick={() => setBookPage(bookPage-1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Prev</button>
                        <span className="text-xs font-bold text-[#585858] uppercase">Halaman {bookPage} / {bookPagination.last_page || 1}</span>
                        <button disabled={bookPage === bookPagination.last_page || !bookPagination.last_page} onClick={() => setBookPage(bookPage+1)} className="px-5 py-2 bg-white border rounded-lg disabled:opacity-50 text-xs font-bold shadow-sm">Next</button>
                    </div>
                </>

            )}
        
            {showModal && (
                <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
                    <div className="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-2xl relative animate-fade-in-up">
                        <button onClick={() => setShowModal(false)} className="absolute top-4 right-4 text-gray-400 hover:text-red-500 font-bold text-xl transition-colors">
                            &times;
                        </button>
                        <h2 className="text-xl font-bold font-montserrat mb-4 text-[#265F9C] border-b pb-2">Cetak Barcode</h2>
                        <div className="flex flex-col items-center justify-center min-h-[200px] bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-4 mb-6">
                            {isGenerating ? (
                                <div className="text-gray-500 flex flex-col items-center">
                                    <span className="text-4xl animate-bounce mb-2">⏳</span>
                                    <p className="text-sm font-medium animate-pulse">Membuat Barcode...</p>
                                </div>
                            ) : (
                                <div dangerouslySetInnerHTML={{ __html: barcodeData }} />
                            )}
                        </div>

                        <div className="flex justify-end gap-3">
                            <button onClick={() => setShowModal(false)} className="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
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

export default ManajemenBuku;