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

    const peminjamInputRef = useRef(null);
    const idPeminjamRef = useRef(idPeminjam);
    const scanningLookupRef = useRef(false);

    useEffect(() => {
        idPeminjamRef.current = idPeminjam;
    }, [idPeminjam]);

    useEffect(() => {
        scanningLookupRef.current = scanningLookup;
    }, [scanningLookup]);

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

    return (
        <div className="bg-white rounded-2xl shadow-lg p-8 max-w-4xl mx-auto border border-gray-100">
            <h1 className="text-2xl font-bold font-montserrat mb-6 text-[#265F9C] flex items-center gap-2">
                Registrasi Peminjaman Buku
            </h1>

            {msg.text && (
                <div className={`mb-6 p-4 rounded-xl text-sm font-bold ${msg.status === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'}`}>
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
        </div>
    );
};

export default PeminjamanPanel;
