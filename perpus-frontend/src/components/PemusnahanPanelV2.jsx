import React, { useEffect, useState } from "react";
import axios from "axios";
import useConfirmDialog from "./useConfirmDialog";

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

const getUserNip = (user) => (
    user?.nip_karyawan ||
    user?.NIP_KARYAWAN ||
    user?.nip ||
    user?.NIP ||
    ""
);

const getUserName = (user) => (
    user?.nama_karyawan ||
    user?.NAMA_KARYAWAN ||
    user?.nama ||
    user?.NAMA ||
    ""
);

const getPetugasName = (row, user) => {
    if (row?.nama_petugas) return row.nama_petugas;

    if (String(row?.nip_karyawan || "") === String(getUserNip(user))) {
        return getUserName(user) || row?.nip_karyawan || "-";
    }

    return row?.nip_karyawan || "-";
};

const cleanAlasan = (value) => String(value || "").replace(/^\[[^\]]+\]\s*/, "");

const toDateTimeLocalValue = (value) => {
    if (!value) return "";

    const date = new Date(String(value).replace(" ", "T"));

    if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 16);
    }

    const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
    return local.toISOString().slice(0, 16);
};

const PemusnahanPanelV2 = ({ user }) => {
    const { confirm, ConfirmDialog } = useConfirmDialog();
    const [tab, setTab] = useState("input");
    const [rows, setRows] = useState([]);
    const [books, setBooks] = useState([]);
    const [search, setSearch] = useState("");
    const [status, setStatus] = useState("semua");
    const [form, setForm] = useState({ isbn: "", alasan: "" });
    const [editForm, setEditForm] = useState({ id: null, isbn: "", alasan: "", tanggal_pemusnahan: "", status: "" });
    const [showEditModal, setShowEditModal] = useState(false);
    const [isSavingEdit, setIsSavingEdit] = useState(false);
    const [isLoadingRows, setIsLoadingRows] = useState(false);
    const [flash, setFlash] = useState({ type: "", text: "" });

    const fetchRows = async (override = {}) => {
        const nextSearch = override.search ?? search;
        const nextStatus = override.status ?? status;

        setIsLoadingRows(true);
        try {
            const response = await axios.get(`${API}/api/pemusnahan`, {
                params: {
                    search: nextSearch,
                    status: tab === "berita" ? "disetujui" : nextStatus,
                }
            });
            setRows(response.data);
        } finally {
            setIsLoadingRows(false);
        }
    };

    useEffect(() => {
        const run = async () => {
            if (tab === "history" || tab === "berita") {
                await fetchRows();
            }
            if (tab === "rusak") {
                const response = await axios.get(`${API}/api/buku-rusak`);
                setBooks(response.data);
            }
        };

        run().catch(() => setFlash({ type: "error", text: "Gagal memuat data pemusnahan." }));
    }, [tab, search, status]);

    const submit = async (isbn, alasan) => {
        await axios.post(`${API}/api/pemusnahan`, { isbn, alasan, nip_karyawan: getUserNip(user) });
        setFlash({ type: "success", text: "Pengajuan pemusnahan berhasil dicatat dan menunggu konfirmasi admin." });
        setForm({ isbn: "", alasan: "" });
        setTab("history");
        setStatus("semua");
    };

    const confirmRow = async (id) => {
        await axios.patch(`${API}/api/pemusnahan/${id}/konfirmasi`, { nip_karyawan: getUserNip(user) });
        setFlash({ type: "success", text: "Pemusnahan disetujui. Berita acara siap dicetak." });
        await fetchRows();
    };

    const openEditModal = (row) => {
        setEditForm({
            id: row.id,
            isbn: row.id_cp_koleksi ? `${row.isbn}/${row.id_cp_koleksi}` : row.isbn,
            alasan: cleanAlasan(row.alasan),
            tanggal_pemusnahan: toDateTimeLocalValue(row.tanggal_pemusnahan),
            status: row.status,
        });
        setShowEditModal(true);
        setFlash({ type: "", text: "" });
    };

    const closeEditModal = () => {
        setShowEditModal(false);
        setEditForm({ id: null, isbn: "", alasan: "", tanggal_pemusnahan: "", status: "" });
        setIsSavingEdit(false);
    };

    const updateRow = async () => {
        setIsSavingEdit(true);
        const response = await axios.put(`${API}/api/pemusnahan/${editForm.id}`, {
            isbn: editForm.isbn,
            alasan: editForm.alasan,
            tanggal_pemusnahan: editForm.tanggal_pemusnahan,
            nip_karyawan: getUserNip(user),
        });
        setFlash({ type: "success", text: "Data pemusnahan berhasil diperbarui." });
        closeEditModal();
        if (response.data?.data) {
            setRows((current) => current.map((row) => row.id === response.data.data.id ? response.data.data : row));
        } else {
            await fetchRows();
        }
    };

    const deleteRow = async (id) => {
        await axios.patch(`${API}/api/pemusnahan/${id}`, { status: "soft_deleted" });
        setFlash({ type: "success", text: "Data pemusnahan berhasil dihapus (soft delete)." });
        await fetchRows();
    };

    const openPrint = (id) => window.open(`${API}/pustakawan/pemusnahan/${id}/berita-acara`, "_blank", "noopener,noreferrer");

    const safeSubmit = async (isbn, alasan) => {
        if (!isbn || !alasan.trim()) return window.alert("ISBN dan alasan wajib diisi.");
        const approved = await confirm({
            title: "Ajukan Pemusnahan",
            message: `Ajukan pemusnahan untuk ISBN ${isbn}?`,
            confirmLabel: "Ya, Ajukan",
            tone: "danger",
        });
        if (!approved) return;
        try { await submit(isbn, alasan); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal mengajukan pemusnahan." }); }
    };

    const safeUpdate = async () => {
        if (!editForm.isbn || !editForm.alasan.trim()) return window.alert("ISBN dan alasan wajib diisi.");
        const approved = await confirm({
            title: "Simpan Perubahan",
            message: "Simpan perubahan data pemusnahan ini?",
            confirmLabel: "Ya, Simpan",
            tone: "primary",
        });
        if (!approved) return;
        try { await updateRow(); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal mengubah data pemusnahan." }); setIsSavingEdit(false); }
    };

    const safeConfirm = async (id) => {
        const approved = await confirm({
            title: "Konfirmasi Pemusnahan",
            message: "Konfirmasi pemusnahan buku ini?",
            confirmLabel: "Ya, Konfirmasi",
            tone: "primary",
        });
        if (!approved) return;
        try { await confirmRow(id); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal mengonfirmasi pemusnahan." }); }
    };

    const safeDelete = async (id) => {
        const approved = await confirm({
            title: "Hapus Data Pemusnahan",
            message: "Apakah Anda yakin ingin menghapus data pemusnahan ini? Data akan disembunyikan namun tetap tersimpan di sistem.",
            confirmLabel: "Ya, Hapus",
            tone: "danger",
        });
        if (!approved) return;
        try { await deleteRow(id); } catch (error) { setFlash({ type: "error", text: error.response?.data?.message || "Gagal menghapus data pemusnahan." }); }
    };

    const tabs = [ ["rusak", "Buku Rusak"], ["history", "Riwayat Proses"], ["berita", "Berita Acara"]];

    return (
        <div className="bg-white rounded-2xl shadow-lg p-8 max-w-6xl mx-auto border border-gray-100">
            <h1 className="text-3xl font-bold font-montserrat text-[#265F9C]">Berita Acara Pemusnahan</h1>
            <p className="mt-3 max-w-3xl text-[#4B5563] leading-7">Buku yang dimusnahkan harus berstatus rusak atau non-aktif, diproses melalui konfirmasi admin, tercatat di log sistem, dan dapat dicetak sebagai berita acara printable.</p>
            <div className="mt-6 flex gap-2 overflow-x-auto border-b pb-4">
                {tabs.map(([id, label]) => <button key={id} onClick={() => setTab(id)} className={`px-5 py-2 rounded-full text-xs font-bold uppercase ${tab === id ? "bg-[#265F9C] text-white" : "bg-gray-100 text-gray-500"}`}>{label}</button>)}
            </div>
            {flash.text && <div className={`mt-6 rounded-xl border px-4 py-3 text-sm font-bold ${flash.type === "success" ? "border-green-200 bg-green-50 text-green-700" : "border-red-200 bg-red-50 text-red-700"}`}>{flash.text}</div>}

            {tab === "input" && <form onSubmit={(e) => { e.preventDefault(); safeSubmit(form.isbn, form.alasan); }} className="mt-6 max-w-xl space-y-4">
                <div className="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-[#265F9C]">
                    <span className="font-bold">Petugas Pustakawan:</span> {getUserName(user) || getUserNip(user) || "-"}
                </div>
                <input value={form.isbn} onChange={(e) => setForm({ ...form, isbn: e.target.value })} className="w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="ISBN buku" />
                <textarea value={form.alasan} onChange={(e) => setForm({ ...form, alasan: e.target.value })} className="h-32 w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Alasan pemusnahan" />
                <button type="submit" className="rounded-xl bg-red-600 px-6 py-4 font-bold text-white hover:bg-red-700">Ajukan Pemusnahan</button>
            </form>}

            {tab === "rusak" && <div className="mt-6 overflow-x-auto">
                <table className="w-full text-left"><thead className="bg-gray-50 text-[10px] font-bold uppercase text-gray-400"><tr><th className="p-4">ISBN</th><th className="p-4">Judul</th><th className="p-4">Status</th><th className="p-4 text-right">Aksi</th></tr></thead><tbody>
                    {books.length > 0 ? books.map((item, index) => <tr key={`${item.isbn}-${index}`} className="border-b text-sm"><td className="p-4 font-mono font-bold text-[#265F9C]">{item.isbn}</td><td className="p-4 font-semibold">{item.judul}</td><td className="p-4">Rusak / Layak Musnah</td><td className="p-4 text-right"><button onClick={() => safeSubmit(item.isbn, "Rusak berat dan tidak layak pakai")} className="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-600 hover:text-white">Ajukan</button></td></tr>) : <tr><td colSpan="4" className="p-10 text-center text-gray-400">Tidak ada data.</td></tr>}
                </tbody></table>
            </div>}

            {tab === "history" && <>
                <div className="mt-6 flex flex-col gap-3 lg:flex-row">
                    <input value={search} onChange={(e) => setSearch(e.target.value)} className="flex-1 rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Cari ID, ISBN/copy, judul, alasan, petugas, rak, atau status..." />
                    <select value={status} onChange={(e) => setStatus(e.target.value)} className="rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                        <option value="disetujui">Disetujui</option>
                    </select>
                    <button onClick={() => fetchRows()} disabled={isLoadingRows} className="rounded-xl bg-[#265F9C] px-6 py-4 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50">{isLoadingRows ? "Mencari..." : "Cari"}</button>
                    <button onClick={() => { setSearch(""); setStatus("semua"); fetchRows({ search: "", status: "semua" }); }} className="rounded-xl bg-gray-100 px-6 py-4 text-sm font-bold text-gray-600 hover:bg-gray-200">Reset</button>
                </div>
                <div className="mt-6 overflow-x-auto">
                    <table className="w-full text-left"><thead className="bg-gray-50 text-[10px] font-bold uppercase text-gray-400"><tr><th className="p-4">Tanggal</th><th className="p-4">ISBN</th><th className="p-4">Judul</th><th className="p-4">Alasan</th><th className="p-4">Status</th><th className="p-4">Petugas</th><th className="p-4 text-right">Aksi</th></tr></thead><tbody>
                        {isLoadingRows ? <tr><td colSpan="7" className="p-10 text-center text-gray-400">Memuat data pemusnahan...</td></tr> : rows.length > 0 ? rows.map((row) => <tr key={row.id} className="border-b text-sm"><td className="p-4"><p>{formatWibDateTime(row.tanggal_pemusnahan)}</p><p className="mt-1 font-mono text-[10px] font-bold text-gray-400">ID #{row.id}</p></td><td className="p-4 font-mono font-bold text-[#265F9C]">{row.id_cp_koleksi ? `${row.isbn}/${row.id_cp_koleksi}` : row.isbn}</td><td className="p-4 font-semibold"><p>{row.judul}</p><p className="mt-1 text-xs font-normal text-gray-400">Rak: {row.no_rak_buku || "-"}</p></td><td className="p-4 max-w-sm">{row.alasan}</td><td className="p-4"><span className={`rounded-full border px-3 py-1 text-[10px] font-bold uppercase ${badgeClass(row.status)}`}>{row.status.replaceAll("_", " ")}</span></td><td className="p-4">{getPetugasName(row, user)}</td><td className="p-4"><div className="flex justify-end gap-2">{["menunggu_konfirmasi", "disetujui"].includes(row.status) && <button onClick={() => openEditModal(row)} className="rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white">Edit</button>}{row.status === "menunggu_konfirmasi" && <button onClick={() => safeConfirm(row.id)} className="rounded-lg bg-[#265F9C] px-3 py-2 text-xs font-bold text-white">Konfirmasi</button>}{row.status === "disetujui" && <button onClick={() => openPrint(row.id)} className="rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white">Cetak BA</button>}<button onClick={() => safeDelete(row.id)} className="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-600 hover:text-white transition-colors">Hapus</button></div></td></tr>) : <tr><td colSpan="7" className="p-10 text-center text-gray-400">Belum ada data pemusnahan.</td></tr>}
                    </tbody></table>
                </div>
            </>}

            {showEditModal && <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                <div className="w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <h2 className="text-xl font-bold text-[#265F9C]">Edit Proses Pemusnahan</h2>
                            <p className="mt-1 text-sm text-gray-500">{editForm.status === "disetujui" ? "Data sudah disetujui. ISBN/copy dikunci, tanggal dan alasan masih bisa diedit." : "Data masih menunggu konfirmasi dan bisa diedit sebelum ACC."}</p>
                        </div>
                        <button type="button" onClick={closeEditModal} className="text-2xl font-bold text-gray-400 hover:text-red-500">&times;</button>
                    </div>
                    <div className="mt-6 space-y-4">
                        <input value={editForm.isbn} onChange={(e) => setEditForm({ ...editForm, isbn: e.target.value })} disabled={editForm.status === "disetujui"} className="w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C] disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500" placeholder="ISBN atau barcode ISBN/ID copy" />
                        <input type="datetime-local" value={editForm.tanggal_pemusnahan} onChange={(e) => setEditForm({ ...editForm, tanggal_pemusnahan: e.target.value })} className="w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" />
                        <textarea value={editForm.alasan} onChange={(e) => setEditForm({ ...editForm, alasan: e.target.value })} className="h-32 w-full rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Alasan pemusnahan" />
                    </div>
                    <div className="mt-6 flex justify-end gap-3">
                        <button type="button" onClick={closeEditModal} className="rounded-xl bg-gray-100 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-200">Batal</button>
                        <button type="button" onClick={safeUpdate} disabled={isSavingEdit} className="rounded-xl bg-[#265F9C] px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50">{isSavingEdit ? "Menyimpan..." : "Simpan Perubahan"}</button>
                    </div>
                </div>
            </div>}

            {tab === "berita" && <>
                <div className="mt-6 flex items-center gap-3">
                    <input value={search} onChange={(e) => setSearch(e.target.value)} className="flex-1 rounded-xl border bg-gray-50 p-4 outline-none focus:ring-2 focus:ring-[#265F9C]" placeholder="Cari berita acara..." />
                    <div className="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">Hanya data yang sudah disetujui admin.</div>
                </div>
                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    {rows.length > 0 ? rows.map((row) => <div key={row.id} className="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"><p className="text-xs font-bold uppercase tracking-[0.3em] text-[#265F9C]">Berita Acara</p><h3 className="mt-2 text-xl font-bold text-[#1F2937]">{row.judul}</h3><p className="mt-2 font-mono text-sm text-gray-500">{row.isbn}</p><div className="mt-4 space-y-2 text-sm text-[#4B5563]"><p><span className="font-bold text-[#1F2937]">Tanggal:</span> {formatWibDateTime(row.updated_at || row.tanggal_pemusnahan)}</p><p><span className="font-bold text-[#1F2937]">Petugas:</span> {getPetugasName(row, user)}</p><p><span className="font-bold text-[#1F2937]">Alasan:</span> {row.alasan}</p></div><div className="mt-5 flex justify-end"><button onClick={() => openPrint(row.id)} className="rounded-xl bg-[#265F9C] px-4 py-3 text-xs font-bold text-white">Buka Printable</button></div></div>) : <div className="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center text-gray-500">Belum ada berita acara yang siap dicetak.</div>}
                </div>
            </>}
            <ConfirmDialog />
        </div>
    );
};

export default PemusnahanPanelV2;
