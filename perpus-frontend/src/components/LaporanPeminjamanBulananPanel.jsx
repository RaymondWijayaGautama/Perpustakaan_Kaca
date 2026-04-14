import { useEffect, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const bulanOptions = [
  { value: '', label: 'Semua Bulan' },
  { value: '1', label: 'Januari' },
  { value: '2', label: 'Februari' },
  { value: '3', label: 'Maret' },
  { value: '4', label: 'April' },
  { value: '5', label: 'Mei' },
  { value: '6', label: 'Juni' },
  { value: '7', label: 'Juli' },
  { value: '8', label: 'Agustus' },
  { value: '9', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
];

const currentYear = new Date().getFullYear();
const tahunOptions = Array.from({ length: 6 }, (_, index) => String(currentYear - index));

const LaporanPeminjamanBulananPanel = () => {
  const [tahun, setTahun] = useState(String(currentYear));
  const [bulan, setBulan] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [report, setReport] = useState({
    filter: { periode_label: '' },
    summary: { total_peminjaman: 0, jumlah_bulan_aktif: 0 },
    data: [],
  });

  useEffect(() => {
    const fetchReport = async () => {
      setLoading(true);
      setError('');

      try {
        const res = await axios.get(`${API_BASE_URL}/laporan/peminjaman-bulanan`, {
          params: {
            tahun,
            bulan,
          },
        });

        setReport(res.data);
      } catch (err) {
        console.error(err);
        setError('Laporan statistik peminjaman gagal dimuat.');
      } finally {
        setLoading(false);
      }
    };

    fetchReport();
  }, [tahun, bulan]);

  return (
    <section className="bg-white rounded-xl shadow p-6 border border-gray-100 text-[#1A1A1A]">
      <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
        <div>
          <h1 className="text-2xl font-bold font-montserrat">Laporan Statistik Peminjaman Buku Bulanan</h1>
          <p className="text-sm text-[#585858] mt-2">
            Data diambil dari transaksi peminjaman dan dapat difilter per bulan maupun tahun.
          </p>
        </div>
        <div className="flex gap-3">
          <select
            className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
            value={bulan}
            onChange={(event) => setBulan(event.target.value)}
          >
            {bulanOptions.map((option) => (
              <option key={option.value || 'all'} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
          <select
            className="p-3 border rounded-xl text-sm bg-gray-50 font-medium"
            value={tahun}
            onChange={(event) => setTahun(event.target.value)}
          >
            {tahunOptions.map((option) => (
              <option key={option} value={option}>
                {option}
              </option>
            ))}
          </select>
        </div>
      </div>

      {error && (
        <div className="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
          {error}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div className="rounded-xl border border-blue-100 bg-blue-50 p-5">
          <p className="text-xs font-bold uppercase tracking-[0.2em] text-[#265F9C]">Periode Laporan</p>
          <p className="mt-3 text-2xl font-bold font-montserrat text-[#1A1A1A]">
            {report.filter.periode_label || '-'}
          </p>
        </div>
        <div className="rounded-xl border border-amber-100 bg-amber-50 p-5">
          <p className="text-xs font-bold uppercase tracking-[0.2em] text-[#EDA60F]">Total Peminjaman</p>
          <p className="mt-3 text-4xl font-bold font-montserrat text-[#1A1A1A]">
            {(report.summary.total_peminjaman || 0).toLocaleString('id-ID')}
          </p>
        </div>
      </div>

      {loading ? (
        <div className="py-16 text-center text-[#585858]">Memuat laporan statistik peminjaman...</div>
      ) : (
        <>
          <table className="w-full text-left">
            <thead className="bg-gray-50 uppercase text-[10px] font-bold text-[#585858] border-b">
              <tr>
                <th className="p-4 w-16 text-center">No</th>
                <th className="p-4">Bulan</th>
                <th className="p-4 text-center">Jumlah Peminjaman</th>
              </tr>
            </thead>
            <tbody>
              {report.data.map((item, index) => (
                <tr key={`${item.nomor_bulan}-${index}`} className="border-b text-sm hover:bg-blue-50/40 transition-colors">
                  <td className="p-4 text-center text-[#7D7D7E]">{index + 1}</td>
                  <td className="p-4 font-semibold">{item.nama_bulan}</td>
                  <td className="p-4 text-center">
                    <span className="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-[#1A1A1A]">
                      {item.jumlah_peminjaman} transaksi
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {report.data.length === 0 && (
            <div className="mt-8 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center text-[#585858]">
              Belum ada transaksi peminjaman pada periode yang dipilih.
            </div>
          )}
        </>
      )}
    </section>
  );
};

export default LaporanPeminjamanBulananPanel;
