import React, { useState, useEffect } from "react";
import axios from "axios";
import { Html5QrcodeScanner } from "html5-qrcode";

const PeminjamanPanel = ({ user }) => {
    const [idCpKoleksi, setIdCpKoleksi] = useState("");
    const [idSiswa, setIdSiswa] = useState("");
    const [msg, setMsg] = useState({ status: "", text: "" });

    const handlePinjam = async (e) => {
        if(e) e.preventDefault();
        setMsg({ status: "loading", text: "Memproses peminjaman..." });

        try {
            await axios.post("http://localhost:8000/api/peminjaman", {
                isbn: idCpKoleksi,  
                id_siswa_tetap: idSiswa,
                nip_karyawan: user.nip_karyawan || "P001" 
            });
            setMsg({ status: "success", text: "Berhasil! Buku resmi dipinjam." });
            setIdCpKoleksi("");
            setIdSiswa("");
        } catch (error) {
            setMsg({ status: "error", text: error.response?.data?.message || "Gagal memproses." });
        }
    };

    useEffect(() => {
        const scanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: { width: 250, height: 150 },
        });

        scanner.render((data) => {
            setIdCpKoleksi(data); 
            scanner.clear(); 
        }, () => {});

        return () => scanner.clear();
    }, []);

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
                    <div id="reader" className="overflow-hidden rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50"></div>
                    <p className="text-[20px] text-gray-500 mt-2 text-center ">Arahkan barcode buku ke scanner</p>
                </div>

                <form onSubmit={handlePinjam} className="space-y-5">
                    <div>
                        <label className="block text-sm font-bold mb-2">ISBN Koleksi Buku Terisi Otomatis</label>
                        <input 
                            type="text" value={idCpKoleksi} onChange={(e) => setIdCpKoleksi(e.target.value)}
                            placeholder="Scan barcode atau ketik ISBN buku"
                            className="w-full p-4 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#265F9C] outline-none font-mono"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-bold mb-2">NISN Peminjam</label>
                        <input 
                            type="text" value={idSiswa} onChange={(e) => setIdSiswa(e.target.value)}
                            placeholder="Masukkan NISN siswa"
                            className="w-full p-4 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#265F9C] outline-none"
                            required
                        />
                    </div>

                    <button 
                        type="submit"
                        className="w-full py-4 bg-[#265F9C] text-white rounded-xl font-bold shadow-lg hover:bg-blue-800 transition-all active:scale-95 flex justify-center items-center gap-2"
                    >
                        Proses Peminjaman
                    </button>
                </form>
            </div>
        </div>
    );
};

export default PeminjamanPanel;