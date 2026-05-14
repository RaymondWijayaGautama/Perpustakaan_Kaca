import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import BarcodeCameraScanner from "./BarcodeCameraScanner";

const getUserNip = (user) => (
    user?.nip_karyawan ||
    user?.NIP_KARYAWAN ||
    user?.nip ||
    user?.NIP ||
    ''
);

const PeminjamanPanel = ({ user }) => {
    const [idCpKoleksi, setIdCpKoleksi] = useState("");
    const [idPeminjam, setIdPeminjam] = useState("");
    const [msg, setMsg] = useState({ status: "", text: "" });
    const [scanningLookup, setScanningLookup] = useState(false);
    const [katalogSearch, setKatalogSearch] = useState("");
    const [katalogStatus, setKatalogStatus] = useState("semua");
    const [katalogData, setKatalogData] = useState({
        data: [],
        current_page: 1,
        last_page: 1,
        total: 0,
    });
    const [loadingKatalog, setLoadingKatalog] = useState(false);

    const peminjamInputRef = useRef(null);
    const idPeminjamRef = useRef(idPeminjam);
    const scanningLookupRef = useRef(false);

    useEffect(() => {
        idPeminjamRef.current = idPeminjam;
    }, [idPeminjam]);

    useEffect(() => {
        scanningLookupRef.current = scanningLookup;
    }, [scanningLookup]);

    const fetchKatalogKoleksi = async (page = 1, override = {}) => {
        setLoadingKatalog(true);

        try {
            const response = await axios.get("http://localhost:8000/api/peminjaman/katalog-koleksi", {
                params: {
                    search: override.search ?? katalogSearch,
                    status: override.status ?? katalogStatus,
                    page,
                    per_page: 8,
                }
            });

            setKatalogData({
                data: response.data?.data || [],
                current_page: response.data?.current_page || 1,
                last_page: response.data?.last_page || 1,
                total: response.data?.total || 0,
            });
        } catch (error) {
            setMsg({ status: "error", text: error.response?.data?.message || "Gagal memuat katalog koleksi." });
            setKatalogData({ data: [], current_page: 1, last_page: 1, total: 0 });
        } finally {
            setLoadingKatalog(false);
        }
    };

    useEffect(() => {
        fetchKatalogKoleksi();
    }, []);

    const handleCariKatalog = (e) => {
        e.preventDefault();
        fetchKatalogKoleksi(1);
    };

    const handleFilterKatalog = (status) => {
        setKatalogStatus(status);
        fetchKatalogKoleksi(1, { status });
    };

    const pilihKoleksi = (item) => {
        if (!item.bisa_dipinjam) {
            setMsg({
                status: "error",
                text: `${item.judul_koleksi} tidak bisa dipinjam karena statusnya ${item.status_ketersediaan}.`
            });
            return;
        }

        const barcode = item.barcode_pinjam || `${item.ISBN}/${item.id_cp_koleksi}`;
        setIdCpKoleksi(barcode);
        setMsg({ status: "info", text: `Copy #${item.id_cp_koleksi} dipilih. Isi NISN/NIP peminjam, lalu proses peminjaman.` });
        setTimeout(() => peminjamInputRef.current?.focus(), 0);
    };

    const prosesPinjamByKode = async (kodeBuku) => {
        const barcodeValue = String(kodeBuku || '').trim();
        const peminjamValue = String(idPeminjamRef.current || '').trim();

        if (!barcodeValue || scanningLookupRef.current) {
            return;
        }

        setIdCpKoleksi(barcodeValue);

        if (!peminjamValue) {
            setMsg({ status: "error", text: `Barcode buku terbaca: ${barcodeValue}. Isi NISN/NIP peminjam dulu, lalu proses peminjaman.` });
            setTimeout(() => peminjamInputRef.current?.focus(), 0);
            return;
        }

        scanningLookupRef.current = true;
        setScanningLookup(true);
        setMsg({ status: "loading", text: "Memproses peminjaman..." });

        try {
            const response = await axios.post("http://localhost:8000/api/peminjaman", {
                isbn: barcodeValue,
                id_peminjam: peminjamValue,
                editor_nip_karyawan: getUserNip(user)
            });

            setMsg({ status: "success", text: response.data?.message || "Berhasil! Buku resmi dipinjam." });
            setIdCpKoleksi("");
            setIdPeminjam("");
            idPeminjamRef.current = "";
            fetchKatalogKoleksi(katalogData.current_page);
        } catch (error) {
            setMsg({ status: "error", text: error.response?.data?.message || "Gagal memproses peminjaman." });
        } finally {
            scanningLookupRef.current = false;
            setScanningLookup(false);
        }
    };

    const handlePinjam = async (e) => {
        if(e) e.preventDefault();

        if (!idCpKoleksi.trim() || !idPeminjam.trim()) {
            setMsg({ status: "error", text: "Barcode buku dan NISN/NIP peminjam wajib diisi." });
            return;
        }

        await prosesPinjamByKode(idCpKoleksi);
    };

    const getMsgClass = () => {
        if (msg.status === "success") {
            return "bg-green-50 text-green-700 border border-green-200";
        }

        if (msg.status === "loading" || msg.status === "info") {
            return "bg-blue-50 text-[#265F9C] border border-blue-200";
        }

        return "bg-red-50 text-red-700 border border-red-200";
    };

    const statusFilterOptions = [
        { value: "semua", label: "Semua" },
        { value: "tersedia", label: "Belum Dipinjam" },
        { value: "dipinjam", label: "Sudah Dipinjam" },
        { value: "tidak_tersedia", label: "Tidak Tersedia" },
    ];

    return (
        <div className="bg-white rounded-2xl shadow-lg p-8 max-w-6xl mx-auto border border-gray-100">
            <h1 className="text-2xl font-bold font-montserrat mb-6 text-[#265F9C] flex items-center gap-2">
                Registrasi Peminjaman Buku
            </h1>

            {msg.text && (
                <div className={`mb-6 p-4 rounded-xl text-sm font-bold ${getMsgClass()}`}>
                    {msg.text}
                </div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div>
                    <label className="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Langkah 1: Scan Barcode Buku</label>
                    <BarcodeCameraScanner
                        readerId="peminjaman-reader"
                        active={!scanningLookup}
                        onScan={(data) => {
                            setIdCpKoleksi(data);
                            prosesPinjamByKode(data);
                        }}
                    />
                </div>

                <form onSubmit={handlePinjam} className="space-y-5">
                    <div>
                        <label className="block text-sm font-bold mb-2">Barcode Koleksi Buku</label>
                        <input 
                            type="text" value={idCpKoleksi} onChange={(e) => setIdCpKoleksi(e.target.value)}
                            placeholder="Scan barcode atau ketik ISBN buku"
                            className="w-full p-4 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#265F9C] outline-none font-mono"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-bold mb-2">NISN / NIP Peminjam</label>
                        <input 
                            ref={peminjamInputRef}
                            type="text" value={idPeminjam} onChange={(e) => {
                                setIdPeminjam(e.target.value);
                                idPeminjamRef.current = e.target.value;
                            }}
                            placeholder="Masukkan NISN siswa atau NIP karyawan"
                            className="w-full p-4 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#265F9C] outline-none"
                            required
                        />
                    </div>

                    <button 
                        type="submit"
                        disabled={scanningLookup}
                        className="w-full py-4 bg-[#265F9C] text-white rounded-xl font-bold shadow-lg hover:bg-blue-800 transition-all active:scale-95 flex justify-center items-center gap-2 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {scanningLookup ? 'Memproses...' : 'Proses Peminjaman'}
                    </button>
                </form>
            </div>

            <div className="mt-10 border-t border-gray-100 pt-8">
                <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 className="text-lg font-black text-[#1A1A1A] uppercase tracking-tight">Daftar Koleksi Siap Pinjam</h2>
                        <p className="mt-1 text-xs font-semibold uppercase tracking-widest text-gray-400">
                            Buku dan laporan PKL beserta status peminjamannya
                        </p>
                    </div>

                    <form onSubmit={handleCariKatalog} className="flex w-full flex-col gap-2 md:w-[460px] md:flex-row">
                        <input
                            type="text"
                            value={katalogSearch}
                            onChange={(e) => setKatalogSearch(e.target.value)}
                            placeholder="Cari judul, ISBN, copy, kategori..."
                            className="flex-1 rounded-xl border bg-gray-50 p-3 text-sm outline-none focus:ring-2 focus:ring-[#265F9C]"
                        />
                        <button
                            type="submit"
                            disabled={loadingKatalog}
                            className="rounded-xl bg-[#265F9C] px-6 py-3 text-sm font-bold uppercase text-white transition-colors hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {loadingKatalog ? "..." : "Cari"}
                        </button>
                    </form>
                </div>

                <div className="mt-5 flex flex-wrap gap-2">
                    {statusFilterOptions.map((option) => (
                        <button
                            key={option.value}
                            type="button"
                            onClick={() => handleFilterKatalog(option.value)}
                            className={`rounded-xl border px-4 py-2 text-xs font-black uppercase tracking-widest transition-colors ${
                                katalogStatus === option.value
                                    ? "border-[#265F9C] bg-[#265F9C] text-white"
                                    : "border-gray-200 bg-white text-gray-500 hover:bg-gray-50"
                            }`}
                        >
                            {option.label}
                        </button>
                    ))}
                    <button
                        type="button"
                        onClick={() => {
                            setKatalogSearch("");
                            setKatalogStatus("semua");
                            fetchKatalogKoleksi(1, { search: "", status: "semua" });
                        }}
                        className="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-xs font-black uppercase tracking-widest text-gray-500 transition-colors hover:bg-gray-100"
                    >
                        Reset
                    </button>
                </div>

                <div className="mt-5 overflow-x-auto rounded-xl border border-gray-100">
                    <table className="w-full min-w-[900px] text-left">
                        <thead className="bg-gray-50 text-xs uppercase tracking-widest text-gray-400">
                            <tr>
                                <th className="p-4">Koleksi</th>
                                <th className="p-4">Copy</th>
                                <th className="p-4">Jenis</th>
                                <th className="p-4">Rak</th>
                                <th className="p-4">Status</th>
                                <th className="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loadingKatalog ? (
                                <tr>
                                    <td colSpan="6" className="p-8 text-center text-sm font-bold uppercase tracking-widest text-gray-400">
                                        Memuat katalog koleksi...
                                    </td>
                                </tr>
                            ) : katalogData.data.length > 0 ? (
                                katalogData.data.map((item) => (
                                    <tr key={item.id_cp_koleksi} className="border-t border-gray-100 text-sm hover:bg-blue-50/30">
                                        <td className="p-4">
                                            <p className="font-bold text-[#1A1A1A]">{item.judul_koleksi}</p>
                                            <p className="mt-1 font-mono text-xs text-gray-400">{item.ISBN}</p>
                                            <p className="mt-1 text-xs text-gray-500">{item.pengarang || "-"}</p>
                                        </td>
                                        <td className="p-4">
                                            <p className="font-mono font-black text-[#265F9C]">#{item.id_cp_koleksi}</p>
                                            <p className="mt-1 font-mono text-xs text-gray-400">{item.barcode_pinjam}</p>
                                        </td>
                                        <td className="p-4 font-bold text-gray-600">{item.jenis_koleksi}</td>
                                        <td className="p-4 font-bold text-gray-600">{item.no_rak_buku || "-"}</td>
                                        <td className="p-4">
                                            <span className={`inline-flex rounded-full px-3 py-1 text-xs font-black uppercase tracking-widest ${
                                                item.status_ketersediaan === "Belum Dipinjam"
                                                    ? "bg-green-50 text-green-700"
                                                    : item.status_ketersediaan === "Sudah Dipinjam"
                                                        ? "bg-red-50 text-red-700"
                                                        : "bg-gray-100 text-gray-500"
                                            }`}>
                                                {item.status_ketersediaan}
                                            </span>
                                            {item.sedang_dipinjam && (
                                                <p className="mt-2 text-xs text-gray-500">
                                                    {item.nama_peminjam || "-"} sampai {item.tgl_harus_kembali || "-"}
                                                </p>
                                            )}
                                        </td>
                                        <td className="p-4 text-right">
                                            <button
                                                type="button"
                                                onClick={() => pilihKoleksi(item)}
                                                disabled={!item.bisa_dipinjam || scanningLookup}
                                                className="rounded-xl bg-slate-900 px-5 py-2 text-xs font-black uppercase tracking-widest text-white transition-colors hover:bg-black disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400"
                                            >
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="6" className="p-8 text-center text-sm font-bold uppercase tracking-widest text-gray-400">
                                        Koleksi tidak ditemukan
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="mt-4 flex flex-col gap-3 text-xs font-bold uppercase tracking-widest text-gray-400 md:flex-row md:items-center md:justify-between">
                    <span>Total {katalogData.total} copy koleksi</span>
                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={() => fetchKatalogKoleksi(Math.max(1, katalogData.current_page - 1))}
                            disabled={loadingKatalog || katalogData.current_page <= 1}
                            className="rounded-lg border border-gray-200 px-3 py-2 text-gray-600 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            Prev
                        </button>
                        <span>Hal {katalogData.current_page} / {katalogData.last_page}</span>
                        <button
                            type="button"
                            onClick={() => fetchKatalogKoleksi(Math.min(katalogData.last_page, katalogData.current_page + 1))}
                            disabled={loadingKatalog || katalogData.current_page >= katalogData.last_page}
                            className="rounded-lg border border-gray-200 px-3 py-2 text-gray-600 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PeminjamanPanel;
