import React, { useEffect, useState } from "react";
import axios from "axios";

const API = "http://localhost:8000";

const badgeClass = (status) => {
    if (status === "disetujui") return "bg-green-100 text-green-700 border-green-200";
    if (status === "menunggu_konfirmasi") return "bg-amber-100 text-amber-700 border-amber-200";
    return "bg-gray-100 text-gray-500 border-gray-200";
};

const formatWibDateTime = (value) => {
    if (!value) return "-";

    const normalized = value.replace(" ", "T");
    const utcLikeValue = normalized.endsWith("Z") ? normalized : `${normalized}Z`;
    const date = new Date(utcLikeValue);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return `${new Intl.DateTimeFormat("id-ID", {
        timeZone: "Asia/Jakarta",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    }).format(date)} WIB`;
};

const PemusnahanPanelV2 = ({ user }) => {
    const [tab, setTab] = useState("input");
    const [rows, setRows] = useState([]);
    const [books, setBooks] = useState([]);
    const [search, setSearch] = useState("");
    const [status, setStatus] = useState("semua");
    const [form, setForm] = useState({ isbn: "", alasan: "" });
    const [flash, setFlash] = useState({ type: "", text: "" });

    useEffect(() => {
        const run = async () => {
            if (tab === "history") {
                const response = await axios.get(`${API}/api/pemusnahan`, { params: { search, status } });
                setRows(response.data);
            }
            if (tab === "berita") {
                const response = await axios.get(`${API}/api/pemusnahan`, { params: { search, status: "disetujui" } });
                setRows(response.data);
            }
            if (tab === "rusak") {
                const response = await axios.get(`${API}/api/buku-rusak`);
                setBooks(response.data);
            }
            if (tab === "overdue") {
                const response = await axios.get(`${API}/api/buku-overdue`);
                setBooks(response.data);
            }
        };

        run().catch(() => setFlash({ type: "error", text: "Gagal memuat data pemusnahan." }));
    }, [tab, search, status]);

    const submit = async (isbn, alasan) => {
        await axios.post(`${API}/api/pemusnahan`, { isbn, alasan, nip_karyawan: user.nip_karyawan });
        setFlash({ type: "success", text: "Pengajuan pemusnahan berhasil dicatat dan menunggu konfirmasi admin." });
        setForm({ isbn: "", alasan: "" });
        setTab("history");
        setStatus("semua");
    };

    const confirmRow = async (id) => {
        await axios.patch(`${API}/api/pemusnahan/${id}/konfirmasi`, { nip_karyawan: user.nip_karyawan });
        setFlash({ type: "success", text: "Pemusnahan disetujui. Berita acara siap dicetak." });
        const response = await axios.get(`${API}/api/pemusnahan`, { params: { search, status } });
        setRows(response.data);
    };

    const archiveRow = async (id) => {
        await axios.patch(`${API}/api/pemusnahan/${id}`, { status: "soft_deleted" });
        setFlash({ type: "success", text: "Data pemusnahan berhasil diarsipkan." });
        const response = await axios.get(`${API}/api/pemusnahan`, { params: { search, status } });
        setRows(response.data);
    };

    const openPrint = (id) => window.open(`${API}/pustakawan/pemusnahan/${id}/berita-acara`, "_blank", "noopener,noreferrer");

    const safeSubmit = async (isbn, alasan) => {
        if (!isbn || !alasan.trim()) return window.alert("ISBN dan alasan wajib diisi.");
        if (!window.confirm(`Ajukan pemusnahan untuk ISBN ${isbn}?`)) return;
        try { await submit(isbn, alasan); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal mengajukan pemusnahan." }); }
    };

    const safeConfirm = async (id) => {
        if (!window.confirm("Konfirmasi pemusnahan buku ini?")) return;
        try { await confirmRow(id); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal mengonfirmasi pemusnahan." }); }
    };

    const tabs = [["input", "Input ISBN"], ["rusak", "Buku Rusak"], ["overdue", "Buku Overdue"], ["history", "Riwayat Proses"], ["berita", "Berita Acara"]];

    return (
        <div className="bg-white rounded-2xl shadow-lg p-8 max-w-6xl mx-auto border border-gray-100">
            <h1 className="text-3xl font-bold font-montserrat text-[#265F9C]">Berita Acara Pemusnahan</h1>
            <p className="mt-3 max-w-3xl text-[#4B5563] leading-7">Buku yang dimusnahkan harus berstatus rusak atau non-aktif, diproses melalui konfirmasi admin, tercatat di log sistem, dan dapat dicetak sebagai berita acara printable.</p>
            <div className="mt-6 flex gap-2 overflow-x-auto border-b pb-4">
                {tabs.map(([id, label]) => <button key={id} onClick={() => setTab(id)} className={`px-5 py-2 rounded-full text-xs font-bold uppercase ${tab === id ? "bg-[#265F9C] text-white" : "bg-gray-100 text-gray-500"}`}>{label}</button>)}
            </div>
            {flash.text && <div className={`mt-6 rounded-xl border px-4 py-3 text-sm font-bold ${flash.type === "success" ? "border-green-200 bg-green-50 text-green-700" : "border-red-200 bg-red-50 text-red-700"}`}>{flash.text}</div>}

            {tab === "input" && <div className="mt-6 max-w-xl space-y-4">
                <input value={form.isbn} onChange={(e) => setForm({ ...form, isbn: e.target.value })} className="w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="ISBN buku" />
                <textarea value={form.alasan} onChange={(e) => setForm({ ...form, alasan: e.target.value })} className="h-32 w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Alasan pemusnahan" />
                <button onClick={() => safeSubmit(form.isbn, form.alasan)} className="rounded-xl bg-red-600 px-6 py-4 font-bold text-white hover:bg-red-700">Ajukan Pemusnahan</button>
            </div>}

            {(tab === "rusak" || tab === "overdue") && <div className="mt-6 overflow-x-auto">
                <table className="w-full text-left"><thead className="bg-gray-50 text-[10px] font-bold uppercase text-gray-400"><tr><th className="p-4">ISBN</th><th className="p-4">Judul</th><th className="p-4">Status</th><th className="p-4 text-right">Aksi</th></tr></thead><tbody>
                    {books.length > 0 ? books.map((item, index) => <tr key={`${item.isbn}-${index}`} className="border-b text-sm"><td className="p-4 font-mono font-bold text-[#265F9C]">{item.isbn}</td><td className="p-4 font-semibold">{item.judul}</td><td className="p-4">{tab === "rusak" ? "Rusak / Layak Musnah" : `${item.hari_terlambat} hari`}</td><td className="p-4 text-right"><button onClick={() => safeSubmit(item.isbn, tab === "rusak" ? "Rusak berat dan tidak layak pakai" : "Non-aktif karena tidak dikembalikan dalam waktu lama")} className="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-600 hover:text-white">Ajukan</button></td></tr>) : <tr><td colSpan="4" className="p-10 text-center text-gray-400">Tidak ada data.</td></tr>}
                </tbody></table>
            </div>}

            {tab === "history" && <>
                <div className="mt-6 flex flex-col gap-3 lg:flex-row">
                    <input value={search} onChange={(e) => setSearch(e.target.value)} className="flex-1 rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Cari ISBN, judul, atau alasan..." />
                    <select value={status} onChange={(e) => setStatus(e.target.value)} className="rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                        <option value="disetujui">Disetujui</option>
                    </select>
                </div>
                <div className="mt-6 overflow-x-auto">
                    <table className="w-full text-left"><thead className="bg-gray-50 text-[10px] font-bold uppercase text-gray-400"><tr><th className="p-4">Tanggal</th><th className="p-4">ISBN</th><th className="p-4">Judul</th><th className="p-4">Alasan</th><th className="p-4">Status</th><th className="p-4">Petugas</th><th className="p-4 text-right">Aksi</th></tr></thead><tbody>
                        {rows.length > 0 ? rows.map((row) => <tr key={row.id} className="border-b text-sm"><td className="p-4">{formatWibDateTime(row.tanggal_pemusnahan)}</td><td className="p-4 font-mono font-bold text-[#265F9C]">{row.isbn}</td><td className="p-4 font-semibold">{row.judul}</td><td className="p-4 max-w-sm">{row.alasan}</td><td className="p-4"><span className={`rounded-full border px-3 py-1 text-[10px] font-bold uppercase ${badgeClass(row.status)}`}>{row.status.replaceAll("_", " ")}</span></td><td className="p-4">{row.nama_petugas || row.nip_karyawan}</td><td className="p-4"><div className="flex justify-end gap-2">{row.status === "menunggu_konfirmasi" && <button onClick={() => safeConfirm(row.id)} className="rounded-lg bg-[#265F9C] px-3 py-2 text-xs font-bold text-white">Konfirmasi</button>}{row.status === "disetujui" && <button onClick={() => openPrint(row.id)} className="rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white">Cetak BA</button>}<button onClick={() => archiveRow(row.id)} className="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600">Arsipkan</button></div></td></tr>) : <tr><td colSpan="7" className="p-10 text-center text-gray-400">Belum ada data pemusnahan.</td></tr>}
                    </tbody></table>
                </div>
            </>}

            {tab === "berita" && <>
                <div className="mt-6 flex items-center gap-3">
                    <input value={search} onChange={(e) => setSearch(e.target.value)} className="flex-1 rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Cari berita acara..." />
                    <div className="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">Hanya data yang sudah disetujui admin.</div>
                </div>
                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    {rows.length > 0 ? rows.map((row) => <div key={row.id} className="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"><p className="text-xs font-bold uppercase tracking-[0.3em] text-[#265F9C]">Berita Acara</p><h3 className="mt-2 text-xl font-bold text-[#1F2937]">{row.judul}</h3><p className="mt-2 font-mono text-sm text-gray-500">{row.isbn}</p><div className="mt-4 space-y-2 text-sm text-[#4B5563]"><p><span className="font-bold text-[#1F2937]">Tanggal:</span> {formatWibDateTime(row.updated_at || row.tanggal_pemusnahan)}</p><p><span className="font-bold text-[#1F2937]">Petugas:</span> {row.nama_petugas || row.nip_karyawan}</p><p><span className="font-bold text-[#1F2937]">Alasan:</span> {row.alasan}</p></div><div className="mt-5 flex justify-end"><button onClick={() => openPrint(row.id)} className="rounded-xl bg-[#265F9C] px-4 py-3 text-xs font-bold text-white">Buka Printable</button></div></div>) : <div className="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center text-gray-500">Belum ada berita acara yang siap dicetak.</div>}
                </div>
            </>}
        </div>
    );
};

export default PemusnahanPanelV2;
