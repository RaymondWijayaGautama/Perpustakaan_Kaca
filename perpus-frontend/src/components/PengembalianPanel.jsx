import { useState, useRef, useEffect } from 'react';
import axios from 'axios';
import BarcodeCameraScanner from './BarcodeCameraScanner';

const PengembalianBulkPanel = () => {
  const [memberInput, setMemberInput] = useState('');
  const [memberData, setMemberData] = useState(null);
  const [tglKembaliManual, setTglKembaliManual] = useState(new Date().toISOString().split('T')[0]);
  const [bukuInput, setBukuInput] = useState('');
  const [daftarKembali, setDaftarKembali] = useState([]);
  const [loading, setLoading] = useState(false);
  const [scanningLookup, setScanningLookup] = useState(false);
  
  // State untuk Pop-up Toast
  const [toast, setToast] = useState({ show: false, type: '', text: '' });

  // ==========================================
  // STATE BARU UNTUK PENCARIAN PENGEMBALIAN
  // ==========================================
  const [searchQuery, setSearchQuery] = useState('');
  const [riwayatData, setRiwayatData] = useState([]);
  const [loadingRiwayat, setLoadingRiwayat] = useState(false);
  const [editingReturn, setEditingReturn] = useState(null);
  const [savingEdit, setSavingEdit] = useState(false);

  const inputBukuRef = useRef(null);
  const memberDataRef = useRef(null);
  const daftarKembaliRef = useRef([]);
  const tglKembaliManualRef = useRef(tglKembaliManual);

  useEffect(() => {
    memberDataRef.current = memberData;
  }, [memberData]);

  useEffect(() => {
    daftarKembaliRef.current = daftarKembali;
  }, [daftarKembali]);

  useEffect(() => {
    tglKembaliManualRef.current = tglKembaliManual;
  }, [tglKembaliManual]);

  const getMemberId = (data = memberDataRef.current) => (
    data?.id_siswa_tetap ||
    data?.ID_SISWA_TETAP ||
    data?.id_member ||
    data?.id_peminjam ||
    data?.id_karyawan ||
    data?.ID_KARYAWAN ||
    data?.nip_karyawan ||
    data?.NIP_KARYAWAN ||
    data?.nip_peminjam ||
    data?.identitas_peminjam ||
    data?.nisn_siswa ||
    data?.NISN_SISWA
  );

  const getMemberName = (data = memberDataRef.current) => (
    data?.nama_siswa_tetap ||
    data?.NAMA_SISWA_TETAP ||
    data?.nama_peminjam ||
    data?.nama_karyawan ||
    data?.NAMA_KARYAWAN ||
    '-'
  );

  // Fungsi pembantu untuk memunculkan Pop-up
  const showToast = (type, text) => {
    setToast({ show: true, type, text });
    setTimeout(() => {
      setToast({ show: false, type: '', text: '' });
    }, 4000);
  };

  // 1. Identifikasi Pemustaka
  const cariMember = async () => {
    try {
      const res = await axios.get(`http://localhost:8000/api/anggota/${memberInput}`);
      setMemberData(res.data);
      setTimeout(() => inputBukuRef.current?.focus(), 0);
    } catch (err) {
      showToast('error', 'IDENTITAS TIDAK DITEMUKAN');
    }
  };

  const syncMemberFromScan = (dataPinjam) => {
    const activeMemberId = getMemberId(memberDataRef.current);
    const scannedMemberId = (
      dataPinjam.id_siswa_tetap ||
      dataPinjam.ID_SISWA_TETAP ||
      dataPinjam.nip_karyawan ||
      dataPinjam.NIP_KARYAWAN ||
      dataPinjam.nip_peminjam ||
      dataPinjam.identitas_peminjam ||
      dataPinjam.nisn_siswa
    );

    if (activeMemberId && scannedMemberId && String(activeMemberId) !== String(scannedMemberId)) {
      return false;
    }

    if (!activeMemberId && scannedMemberId) {
      const scannedMember = {
        id_siswa_tetap: scannedMemberId,
        nip_karyawan: dataPinjam.nip_karyawan || dataPinjam.nip_peminjam,
        NISN_SISWA: dataPinjam.nisn_siswa,
        NIP_KARYAWAN: dataPinjam.nip_karyawan || dataPinjam.nip_peminjam,
        identitas_peminjam: dataPinjam.identitas_peminjam,
        nama_siswa_tetap: dataPinjam.nama_peminjam,
        nama_karyawan: dataPinjam.nama_peminjam,
        nama_peminjam: dataPinjam.nama_peminjam,
      };

      setMemberData(scannedMember);
      setMemberInput(dataPinjam.identitas_peminjam || dataPinjam.nisn_siswa || dataPinjam.nip_karyawan || dataPinjam.nip_peminjam || '');
      memberDataRef.current = scannedMember;
    }

    return true;
  };

  const masukDaftarPengembalian = (dataPinjam) => {
    if (daftarKembaliRef.current.some(item => item.id_peminjaman === dataPinjam.id_peminjaman)) {
      showToast('error', 'BUKU SUDAH ADA DI DALAM DAFTAR PENGEMBALIAN');
      setBukuInput('');
      return;
    }

    const deadline = new Date(dataPinjam.tgl_harus_kembali);
    const realita = new Date(tglKembaliManualRef.current);
    const terlambat = realita > deadline ? Math.ceil((realita - deadline) / (1000 * 60 * 60 * 24)) : 0;

    setDaftarKembali(current => [...current, {
      ...dataPinjam,
      kondisi: 'Baik',
      tgl_kembali_manual: tglKembaliManualRef.current,
      estimasi_terlambat: terlambat,
      denda: 0 // Inisialisasi input denda ke 0
    }]);

    setBukuInput('');
    inputBukuRef.current?.focus();
    showToast('success', 'BUKU BERHASIL MASUK DAFTAR PENGEMBALIAN');
  };

  // 2. Tambah Buku ke Daftar
  const tambahBukuByKode = async (kodeBuku) => {
    const kode = String(kodeBuku || '').trim();

    if (kode === '' || scanningLookup) return;

    setScanningLookup(true);
    try {
      const res = await axios.post(`http://localhost:8000/api/pengembalian/scan`, {
        barcode: kode,
        tgl_kembali_manual: tglKembaliManualRef.current
      });

      const dataPinjam = res.data;

      if (!syncMemberFromScan(dataPinjam)) {
        showToast('error', 'BUKU INI DIPINJAM OLEH PEMUSTAKA LAIN');
        return;
      }

      masukDaftarPengembalian(dataPinjam);
    } catch (err) {
      showToast('error', err.response?.data?.message || 'BARCODE TIDAK TERDAFTAR PADA PINJAMAN AKTIF');
    } finally {
      setScanningLookup(false);
    }
  };

  const tambahBuku = async (e) => {
    e.preventDefault();
    await tambahBukuByKode(bukuInput);
  };

  // 3. Eksekusi ke Backend
  const prosesPengembalian = async () => {
    // Validasi denda
    const adaErrorDenda = daftarKembali.some(item => {
        const deskripsi = (item.kondisi || '').trim().toLowerCase();
        const denda = item.denda || 0;
        // Jika deskripsi bukan 'baik' dan tidak kosong, denda harus diisi.
        return deskripsi !== 'baik' && deskripsi !== '' && denda <= 0;
    });

    if (adaErrorDenda) {
      showToast('error', 'NOMINAL DENDA WAJIB DIISI JIKA ADA DESKRIPSI KERUSAKAN');
      return;
    }

    setLoading(true);
    try {
      await axios.post('http://localhost:8000/api/pengembalian/batch', {
        items: daftarKembali
      });
      
      showToast('success', `${daftarKembali.length} KOLEKSI BERHASIL DIKEMBALIKAN (A.N. ${getMemberName(memberData)})`);
      
      setDaftarKembali([]);
      setMemberData(null);
      setMemberInput('');
      setBukuInput('');
      
      // Auto refresh riwayat setelah pengembalian sukses
      fetchRiwayatPengembalian();
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'GAGAL MEMPROSES DATA';
      showToast('error', `ERROR: ${errorMessage}`);
    } finally {
      setLoading(false);
    }
  };

  // ==========================================
  // FUNGSI BARU: FETCH SEMUA DATA & PENCARIAN
  // ==========================================
  const fetchRiwayatPengembalian = async () => {
    setLoadingRiwayat(true);
    try {
      const res = await axios.get(`http://localhost:8000/api/pengembalian/history`, {
        params: { search: searchQuery }
      });
      
      // Karena kita udah ngerapihin di Controller pakai PHP (formattedData), 
      // strukturnya pasti rapi masuk ke res.data.data
      setRiwayatData(res.data.data || []);
    } catch (err) {
      // PERBAIKAN: Kalau ada error dari Controller, sekarang bakal langsung muncul di Pop-up biar lo tau salahnya apa!
      const errorMsg = err.response?.data?.message || err.message;
      showToast('error', `SERVER ERROR: ${errorMsg}`);
      setRiwayatData([]);
    } finally {
      setLoadingRiwayat(false);
    }
  };

  // Jalanin saat komponen pertama kali dirender
  useEffect(() => {
    fetchRiwayatPengembalian();
  }, []);

  // Trigger pencarian saat tombol "Cari" diklik (atau pas form disubmit)
  const handleSearchRiwayat = (e) => {
    e.preventDefault();
    fetchRiwayatPengembalian();
  };

  const toDateInputValue = (value) => String(value || '').split('T')[0].split(' ')[0];

  const openEditPengembalian = (item) => {
    setEditingReturn({
      id_peminjaman: item.id_peminjaman,
      judul_koleksi: item.judul_koleksi || '-',
      nama_peminjam: item.nama_peminjam || '-',
      nisn_nip: item.nisn_nip || '-',
      tgl_kembali: toDateInputValue(item.tgl_kembali),
      kondisi_buku_kembali: item.kondisi_buku_kembali || 'Baik',
      denda: item.denda ?? 0,
      keterangan_peminjaman: item.keterangan_peminjaman || '',
    });
  };

  const updateEditField = (field, value) => {
    setEditingReturn(current => ({
      ...current,
      [field]: value,
    }));
  };

  const simpanEditPengembalian = async (e) => {
    e.preventDefault();

    if (!editingReturn?.id_peminjaman) return;

    setSavingEdit(true);
    try {
      const res = await axios.put(`http://localhost:8000/api/pengembalian/${editingReturn.id_peminjaman}`, {
        tgl_kembali: editingReturn.tgl_kembali,
        kondisi_buku_kembali: editingReturn.kondisi_buku_kembali,
        denda: editingReturn.denda === '' ? 0 : Number(editingReturn.denda),
        keterangan_peminjaman: editingReturn.keterangan_peminjaman,
      });

      const updated = res.data?.data;

      if (updated) {
        setRiwayatData(current => current.map(item => (
          item.id_peminjaman === updated.id_peminjaman ? updated : item
        )));
      } else {
        fetchRiwayatPengembalian();
      }

      setEditingReturn(null);
      showToast('success', res.data?.message || 'DATA PENGEMBALIAN BERHASIL DIPERBARUI');
    } catch (err) {
      showToast('error', err.response?.data?.message || 'GAGAL UPDATE DATA PENGEMBALIAN');
    } finally {
      setSavingEdit(false);
    }
  };

  return (
    <div className="relative p-10 bg-white border border-slate-200 rounded shadow-sm max-w-5xl mx-auto font-mono text-xs overflow-hidden">
      
      {/* ========================================= */}
      {/* POP-UP TOAST */}
      {/* ========================================= */}
      {toast.show && (
        <div className="fixed top-6 right-6 z-50 animate-bounce">
          <div className={`flex items-center justify-between min-w-[300px] p-4 rounded shadow-2xl border-l-4 ${toast.type === 'success' ? 'bg-slate-900 border-green-500 text-white' : 'bg-red-50 border-red-600 text-red-800'}`}>
            <div className="flex items-center gap-3">
              <span className="text-xl">{toast.type === 'success' ? '✅' : '⚠️'}</span>
              <span className="font-bold uppercase tracking-widest leading-relaxed">
                {toast.text}
              </span>
            </div>
            <button onClick={() => setToast({ show: false, type: '', text: '' })} className="ml-6 text-slate-400 hover:text-white font-bold">
              ✕
            </button>
          </div>
        </div>
      )}

      {/* ========================================= */}
      {/* BAGIAN ATAS: FORM ENTRY */}
      {/* ========================================= */}
      <h2 className="text-slate-900 font-bold uppercase tracking-tighter mb-10 border-b pb-4 text-lg">
        Form Entry Pengembalian Koleksi
      </h2>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
        <div className="space-y-4">
          <label className="font-bold text-slate-400 uppercase tracking-widest block">01. Identitas Pemustaka</label>
          <div className="flex gap-2">
            <input 
              type="text" 
              value={memberInput} 
              onChange={(e) => setMemberInput(e.target.value)} 
              className="flex-1 p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50"
              placeholder="NIP / NISN"
            />
            <button onClick={cariMember} className="bg-slate-900 text-white px-6 py-2 font-bold uppercase hover:bg-black transition-colors">Cari</button>
          </div>
          {memberData && (
            <div className="p-4 bg-slate-100 border-l-4 border-slate-900">
              <p className="font-bold uppercase">{getMemberName(memberData)}</p>
              <p className="text-slate-500 uppercase">Status Terverifikasi</p>
            </div>
          )}
        </div>

        <div className="space-y-4">
          <label className="font-bold text-slate-400 uppercase tracking-widest block">02. Parameter Tanggal</label>
          <input 
            type="date" 
            value={tglKembaliManual} 
            onChange={(e) => setTglKembaliManual(e.target.value)}
            className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold"
          />
          <p className="text-[10px] text-slate-400 leading-relaxed italic">
            * Sesuaikan tanggal jika pengembalian fisik terjadi di masa lalu.
          </p>
        </div>
      </div>

      <div className="mb-10 p-6 bg-slate-50 border border-slate-200">
        <div className="mb-4 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
          <div>
            <label className="font-bold text-slate-400 uppercase tracking-widest block">03. Scan Buku Kembali</label>
            <p className="mt-1 text-[10px] text-slate-400 leading-relaxed uppercase tracking-widest">
              Scan barcode buku untuk mencari pinjaman aktif dan mengisi pemustaka otomatis.
            </p>
          </div>
          {scanningLookup && (
            <span className="w-fit bg-blue-50 text-[#265F9C] border border-blue-100 px-3 py-1 text-[10px] font-bold uppercase tracking-widest">
              Memeriksa barcode...
            </span>
          )}
        </div>

        <div className="grid grid-cols-1 md:grid-cols-[320px_1fr] gap-6">
          <BarcodeCameraScanner
            readerId="pengembalian-reader"
            panelClassName="text-slate-900"
            active={!scanningLookup}
            onScan={(data) => {
              setBukuInput(data);
              tambahBukuByKode(data);
            }}
          />
          <div className="space-y-4 self-start">
            <form onSubmit={tambahBuku} className="flex gap-2">
              <input
                ref={inputBukuRef}
                type="text"
                value={bukuInput}
                onChange={(e) => setBukuInput(e.target.value)}
                className="flex-1 p-3 border border-slate-300 outline-none focus:border-slate-900 font-bold"
                placeholder="Scan barcode atau input manual ISBN/ID buku"
              />
              <button
                type="submit"
                disabled={scanningLookup}
                className="bg-slate-200 text-slate-900 border border-slate-300 px-8 py-2 font-bold uppercase hover:bg-slate-300 transition-colors disabled:cursor-not-allowed disabled:opacity-50"
              >
                Input
              </button>
            </form>

            {!memberData && (
              <div className="p-4 bg-blue-50 border-l-4 border-[#265F9C] text-[#265F9C]">
                <p className="font-bold uppercase">Scan buku terlebih dahulu juga bisa.</p>
                <p className="mt-1 text-[10px] uppercase tracking-widest">
                  Sistem akan mencari transaksi aktif dan menampilkan pemustakanya otomatis.
                </p>
              </div>
            )}
          </div>
        </div>
      </div>

      {daftarKembali.length > 0 && (
        <div className="mb-10 overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-slate-200 text-slate-400">
                <th className="py-4 uppercase tracking-widest">Judul Koleksi</th>
                <th className="py-4 uppercase tracking-widest">Deadline</th>
                <th className="py-4 uppercase tracking-widest">Status Kalkulasi</th>
                <th className="py-4 uppercase tracking-widest">Deskripsi (Opsional)</th>
                <th className="py-4 uppercase tracking-widest text-right">Nominal Denda (Rp)</th>
                <th className="py-4 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {daftarKembali.map((item, index) => (
                <tr key={index} className="border-b border-slate-100">
                  <td className="py-4 font-bold uppercase">{item.judul_koleksi}</td>
                  <td className="py-4 text-slate-500">{item.tgl_harus_kembali}</td>
                  <td className="py-4">
                    {item.estimasi_terlambat > 0 ? (
                      <span className="text-red-700 font-bold bg-red-50 px-2 py-1 border border-red-200">TERLAMBAT {item.estimasi_terlambat} HARI (SP 1)</span>
                    ) : (
                      <span className="text-slate-900 font-bold">TEPAT WAKTU</span>
                    )}
                  </td>
                  <td className="py-4">
                    <input 
                      type="text"
                      className="p-1 border border-slate-200 outline-none w-full bg-white font-bold uppercase placeholder:normal-case placeholder:font-normal placeholder:text-xs"
                      value={item.kondisi === 'Baik' ? '' : item.kondisi}
                      placeholder="Baik / Keterangan Rusak"
                      onChange={(e) => {
                        const newDaftar = [...daftarKembali];
                        newDaftar[index].kondisi = e.target.value === '' ? 'Baik' : e.target.value;
                        setDaftarKembali(newDaftar);
                      }}
                    />
                  </td>
                  <td className="py-4 text-right">
                    <input
                      type="number"
                      min="0"
                      className="p-1 border border-slate-200 outline-none w-full max-w-[100px] bg-white font-bold text-right"
                      value={item.denda}
                      placeholder="0"
                      onChange={(e) => {
                        const newDaftar = [...daftarKembali];
                        newDaftar[index].denda = e.target.value === '' ? '' : Number(e.target.value);
                        setDaftarKembali(newDaftar);
                      }}
                    />
                  </td>
                  <td className="py-4 text-right">
                    <button onClick={() => setDaftarKembali(daftarKembali.filter((_, i) => i !== index))} className="text-red-600 font-bold hover:text-red-800 transition-colors uppercase">Cancel</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          <button 
            onClick={prosesPengembalian}
            disabled={loading}
            className="w-full mt-10 bg-slate-900 text-white py-4 font-bold uppercase tracking-[0.2em] hover:bg-black transition-all disabled:bg-slate-400 cursor-pointer disabled:cursor-not-allowed"
          >
            {loading ? 'Processing Data...' : 'Submit Seluruh Pengembalian'}
          </button>
        </div>
      )}

      {/* ========================================= */}
      {/* BAGIAN BAWAH: RIWAYAT PENGEMBALIAN */}
      {/* ========================================= */}
      
      <hr className="my-12 border-slate-200" />
      
      <div className="pt-2">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 border-b border-slate-200 pb-4 gap-4">
          <div>
            <h2 className="text-slate-900 font-bold uppercase tracking-tighter text-lg">
              Data Pengembalian Koleksi
            </h2>
            <p className="text-slate-400 font-mono mt-1 text-[10px] uppercase tracking-widest">
              Riwayat Transaksi Pengembalian Buku Perpustakaan
            </p>
          </div>
          
          {/* Form Pencarian */}
          <div className="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <form onSubmit={handleSearchRiwayat} className="flex gap-2 w-full md:w-[400px]">
            <input 
              type="text" 
              value={searchQuery} 
              onChange={(e) => setSearchQuery(e.target.value)}
              className="flex-1 p-2 border border-slate-300 outline-none focus:border-slate-900 bg-white"
              placeholder="Cari NIP/NISN atau Nama..."
            />
            <button 
              type="submit" 
              disabled={loadingRiwayat} 
              className="bg-slate-900 text-white px-6 py-2 font-bold uppercase hover:bg-black transition-colors disabled:bg-slate-400"
            >
              {loadingRiwayat ? '...' : 'Cari'}
            </button>
            <button
              type="button"
              onClick={() => {
                setSearchQuery('');
                setTimeout(() => {
                  setSearchQuery('');
                  fetchRiwayatPengembalian();
                }, 0);
              }}
              className="bg-slate-100 text-slate-500 border border-slate-300 px-4 py-2 font-bold uppercase hover:bg-slate-200 transition-colors"
            >
              Reset
            </button>
          </form>
          <button
            type="button"
            onClick={async () => {
              try {
                const res = await axios.get('http://localhost:8000/api/pengembalian/export', {
                  params: { search: searchQuery },
                  responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'pengembalian.xlsx');
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
              } catch (err) {
                showToast('error', 'GAGAL EXPORT DATA');
              }
            }}
            className="bg-green-700 text-white px-6 py-2 font-bold uppercase hover:bg-green-800 transition-colors"
          >
            Export
          </button>
        </div>
        </div>

        {/* Tabel Data */}
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse bg-white border border-slate-200">
            <thead>
              <tr className="border-b border-slate-300 text-slate-900 bg-slate-50">
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">No. Pinjam</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Koleksi</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Tgl Kembali</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Pemustaka</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Kondisi</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold border-r border-slate-200">Denda</th>
                <th className="py-3 px-4 uppercase tracking-widest font-bold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {loadingRiwayat ? (
                <tr>
                  <td colSpan="7" className="py-8 text-center text-slate-400 font-bold uppercase tracking-widest border-b border-slate-200">
                    Memuat Data...
                  </td>
                </tr>
              ) : riwayatData.length > 0 ? (
                riwayatData.map((item, index) => (
                  <tr key={index} className="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <td className="py-3 px-4 font-bold border-r border-slate-100">{item.id_peminjaman || '-'}</td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <span className="font-bold uppercase">{item.judul_koleksi || '-'}</span>
                      <br/>
                      <span className="text-[10px] text-slate-400">{item.ISBN || '-'}</span>
                    </td>
                    <td className="py-3 px-4 text-slate-600 border-r border-slate-100">{item.tgl_kembali || '-'}</td>
                    <td className="py-3 px-4 border-r border-slate-100">
                      <span className="font-bold uppercase">{item.nama_peminjam || '-'}</span>
                      <br/>
                      <span className="text-[10px] text-slate-400">{item.nisn_nip || '-'}</span>
                    </td>
                    <td className="py-3 px-4 uppercase border-r border-slate-100">{item.kondisi_buku_kembali || '-'}</td>
                    <td className="py-3 px-4">
                      {item.denda && item.denda > 0 ? (
                        <span className="text-red-700 font-bold bg-red-50 px-2 py-1 border border-red-200 text-[10px]">RP {Number(item.denda).toLocaleString('id-ID')}</span>
                      ) : (
                        <span className="text-slate-500 font-bold text-[10px]">-</span>
                      )}
                    </td>
                    <td className="py-3 px-4 text-right">
                      <button
                        type="button"
                        onClick={() => openEditPengembalian(item)}
                        className="bg-slate-900 text-white px-4 py-2 font-bold uppercase hover:bg-black transition-colors"
                      >
                        Edit
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="7" className="py-8 text-center text-slate-400 font-bold uppercase tracking-widest border-b border-slate-200">
                    Tidak Ada Data Pengembalian
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {editingReturn && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
          <form onSubmit={simpanEditPengembalian} className="w-full max-w-xl bg-white border border-slate-200 rounded shadow-2xl p-6">
            <div className="mb-6 border-b border-slate-200 pb-4">
              <h3 className="text-slate-900 font-bold uppercase tracking-tighter text-lg">
                Edit Data Pengembalian
              </h3>
              <p className="mt-1 text-[10px] text-slate-400 uppercase tracking-widest">
                No. Pinjam #{editingReturn.id_peminjaman}
              </p>
            </div>

            <div className="mb-5 bg-slate-50 border-l-4 border-slate-900 p-4">
              <p className="font-bold uppercase text-slate-900">{editingReturn.judul_koleksi}</p>
              <p className="mt-1 text-[10px] uppercase tracking-widest text-slate-500">
                {editingReturn.nama_peminjam} / {editingReturn.nisn_nip}
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-bold text-slate-400 uppercase tracking-widest block mb-2">Tanggal Kembali</label>
                <input
                  type="date"
                  value={editingReturn.tgl_kembali}
                  onChange={(e) => updateEditField('tgl_kembali', e.target.value)}
                  className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold"
                  required
                />
              </div>

              <div>
                <label className="font-bold text-slate-400 uppercase tracking-widest block mb-2">Kondisi</label>
                <select
                  value={editingReturn.kondisi_buku_kembali}
                  onChange={(e) => updateEditField('kondisi_buku_kembali', e.target.value)}
                  className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold uppercase"
                  required
                >
                  <option value="Baik">BAIK</option>
                  <option value="Rusak">RUSAK</option>
                  <option value="Hilang">HILANG</option>
                </select>
              </div>

              <div>
                <label className="font-bold text-slate-400 uppercase tracking-widest block mb-2">Denda (Rp)</label>
                <input
                  type="number"
                  min="0"
                  value={editingReturn.denda}
                  onChange={(e) => updateEditField('denda', e.target.value === '' ? '' : Number(e.target.value))}
                  className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold"
                  placeholder="0"
                />
              </div>

              <div>
                <label className="font-bold text-slate-400 uppercase tracking-widest block mb-2">Keterangan</label>
                <input
                  type="text"
                  value={editingReturn.keterangan_peminjaman}
                  onChange={(e) => updateEditField('keterangan_peminjaman', e.target.value)}
                  className="w-full p-2 border border-slate-300 outline-none focus:border-slate-900 bg-slate-50 font-bold"
                  placeholder="Catatan pengembalian"
                />
              </div>
            </div>

            <div className="mt-8 flex justify-end gap-3">
              <button
                type="button"
                onClick={() => setEditingReturn(null)}
                disabled={savingEdit}
                className="bg-slate-100 text-slate-500 border border-slate-300 px-5 py-2 font-bold uppercase hover:bg-slate-200 transition-colors disabled:cursor-not-allowed disabled:opacity-50"
              >
                Batal
              </button>
              <button
                type="submit"
                disabled={savingEdit}
                className="bg-slate-900 text-white px-6 py-2 font-bold uppercase hover:bg-black transition-colors disabled:bg-slate-400 disabled:cursor-not-allowed"
              >
                {savingEdit ? 'Menyimpan...' : 'Simpan Perubahan'}
              </button>
            </div>
          </form>
        </div>
      )}

    </div>
  );
};

export default PengembalianBulkPanel;
