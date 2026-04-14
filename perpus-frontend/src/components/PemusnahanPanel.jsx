import React, { useState, useEffect } from "react";
import axios from "axios";

const PemusnahanPanel = ({ user }) => {
    const [tab, setTab] = useState("input"); // input, rusak, overdue, history
    const [dataBuku, setDataBuku] = useState([]);
    const [history, setHistory] = useState([]);
    const [searchQuery, setSearchQuery] = useState("");
    const [formData, setFormData] = useState({ isbn: "", alasan: "" });
    const [msg, setMsg] = useState({ status: "", text: "" });

    // 1. Ambil data buku berdasarkan kondisi (Rusak / Overdue)
    const fetchBukuKondisi = async (endpoint) => {
        try {
            const response = await axios.get(`http://localhost:8000/api/${endpoint}`);
            setDataBuku(response.data); // Asumsi API mengembalikan data beserta ISBN
        } catch (error) {
            console.error("Gagal mengambil data", error);
        }
    };

    // 2. Ambil riwayat pemusnahan (mendukung pencarian ISBN/Judul)
    const fetchHistory = async () => {
        try {
            const response = await axios.get(`http://localhost:8000/api/pemusnahan`, {
                params: { search: searchQuery }
            });
            setHistory(response.data);
        } catch (error) {
            console.error("Gagal mengambil riwayat", error);
        }
    };

    useEffect(() => {
        if (tab === "rusak") fetchBukuKondisi("buku-rusak");
        if (tab === "overdue") fetchBukuKondisi("buku-overdue");
        if (tab === "history") fetchHistory();
    }, [tab, searchQuery]);

    // 3. Proses Simpan Pemusnahan menggunakan ISBN
    const handleMusnahkan = async (isbn, alasanDefault = "") => {
        if (!isbn) return alert("ISBN tidak boleh kosong!");
        
        const confirm = window.confirm(`Apakah Anda yakin ingin memusnahkan buku dengan ISBN: ${isbn}?`);
        if (!confirm) return;

        try {
            await axios.post("http://localhost:8000/api/pemusnahan", {
                isbn: isbn,
                alasan: alasanDefault || formData.alasan,
                nip_karyawan: user.nip_karyawan || "P001"
            });
            setMsg({ status: "success", text: `Buku ISBN ${isbn} berhasil dicatat untuk dimusnahkan.` });
            setFormData({ isbn: "", alasan: "" });
            setTab("history");
        } catch (error) {
            setMsg({ status: "error", text: error.response?.data?.message || "Gagal memproses. Pastikan ISBN terdaftar." });
        }
    };

    // 4. Update Status (Soft Delete)
    const updateStatusPemusnahan = async (id, statusBaru) => {
        try {
            await axios.patch(`http://localhost:8000/api/pemusnahan/${id}`, { status: statusBaru });
            fetchHistory();
        } catch (error) {
            alert("Gagal memperbarui status.");
        }
    };

    return (
        <div className="bg-white rounded-2xl shadow-lg p-8 max-w-6xl mx-auto border border-gray-100">
            <h1 className="text-2xl font-bold font-montserrat mb-6 text-[#265F9C] flex items-center gap-2">
                Registrasi Pemusnahan Buku (ISBN)
            </h1>

            {/* Tab Navigasi */}
            <div className="flex gap-2 mb-8 border-b pb-4 overflow-x-auto">
                {[
                    { id: "input", label: "Input ISBN" },
                    { id: "rusak", label: "Cek Buku Rusak" },
                    { id: "overdue", label: "Cek Overdue" },
                    { id: "history", label: "Riwayat & Cari" },
                ].map((t) => (
                    <button
                        key={t.id}
                        onClick={() => setTab(t.id)}
                        className={`px-6 py-2 rounded-full text-xs font-bold uppercase transition-all ${
                            tab === t.id ? "bg-[#265F9C] text-white shadow-md" : "bg-gray-100 text-gray-500 hover:bg-gray-200"
                        }`}
                    >
                        {t.label}
                    </button>
                ))}
            </div>

            {msg.text && (
                <div className={`mb-6 p-4 rounded-xl text-sm font-bold border ${msg.status === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'}`}>
                    {msg.text}
                </div>
            )}

            {/* TAB 1: INPUT MANUAL ISBN */}
            {tab === "input" && (
                <div className="max-w-md space-y-5">
                    <div>
                        <label className="block text-sm font-bold mb-2">Nomor ISBN Buku</label>
                        <input 
                            type="text" 
                            value={formData.isbn}
                            className="w-full p-4 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-[#265F9C]" 
                            placeholder="Contoh: 978-602-03-1234-5"
                            onChange={(e) => setFormData({...formData, isbn: e.target.value})}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-bold mb-2">Alasan Pemusnahan</label>
                        <textarea 
                            value={formData.alasan}
                            className="w-full p-4 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-[#265F9C] h-32" 
                            placeholder="Jelaskan kondisi buku (Rusak berat/Hilang)..."
                            onChange={(e) => setFormData({...formData, alasan: e.target.value})}
                        ></textarea>
                    </div>
                    <button 
                        onClick={() => handleMusnahkan(formData.isbn)}
                        className="w-full py-4 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all shadow-lg active:scale-95"
                    >
                        Simpan Data Pemusnahan
                    </button>
                </div>
            )}

            {/* TAB 2 & 3: DAFTAR BUKU RUSAK / OVERDUE */}
            {(tab === "rusak" || tab === "overdue") && (
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 border-b">
                            <tr>
                                <th className="p-4">ISBN</th>
                                <th className="p-4">Judul Buku</th>
                                <th className="p-4">{tab === "rusak" ? "Status" : "Keterlambatan"}</th>
                                <th className="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {dataBuku.length > 0 ? dataBuku.map((item, index) => (
                                <tr key={index} className="border-b hover:bg-gray-50 transition-colors">
                                    <td className="p-4 font-mono font-bold text-[#265F9C]">{item.isbn}</td>
                                    <td className="p-4 text-sm font-medium">{item.judul}</td>
                                    <td className="p-4 text-xs">
                                        <span className="px-2 py-1 bg-orange-100 text-orange-700 rounded font-bold">
                                            {tab === "rusak" ? "Kondisi Rusak" : `${item.hari_terlambat} Hari`}
                                        </span>
                                    </td>
                                    <td className="p-4 text-right">
                                        <button 
                                            onClick={() => handleMusnahkan(item.isbn, tab === "rusak" ? "Kondisi Rusak Berat" : "Buku Tidak Dikembalikan")}
                                            className="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition-all"
                                        >
                                            Musnahkan
                                        </button>
                                    </td>
                                </tr>
                            )) : (
                                <tr><td colSpan="4" className="p-10 text-center text-gray-400">Tidak ada data buku {tab}.</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            )}

            {/* TAB 4: RIWAYAT & PENCARIAN */}
            {tab === "history" && (
                <div>
                    <div className="mb-6">
                        <input 
                            type="text"
                            placeholder="Cari berdasarkan ISBN atau Judul Buku..."
                            className="w-full p-4 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-[#265F9C] shadow-sm"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 border-b">
                                <tr>
                                    <th className="p-4">Tgl Input</th>
                                    <th className="p-4">ISBN</th>
                                    <th className="p-4">Alasan</th>
                                    <th className="p-4">Status</th>
                                    <th className="p-4 text-right">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {history.map((h) => (
                                    <tr key={h.id} className="border-b text-sm hover:bg-gray-50">
                                        <td className="p-4 text-gray-500">{h.tanggal_pemusnahan}</td>
                                        <td className="p-4 font-mono font-bold text-[#265F9C]">{h.isbn}</td>
                                        <td className="p-4">{h.alasan}</td>
                                        <td className="p-4">
                                            <span className={`px-2 py-1 rounded text-[10px] font-bold uppercase ${h.status === 'dimusnahkan' ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-600'}`}>
                                                {h.status}
                                            </span>
                                        </td>
                                        <td className="p-4 text-right">
                                            <button 
                                                onClick={() => updateStatusPemusnahan(h.id, "soft_deleted")}
                                                className="text-gray-400 hover:text-red-600 font-bold text-xs uppercase"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PemusnahanPanel;