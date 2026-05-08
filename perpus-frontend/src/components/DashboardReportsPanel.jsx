import { useEffect, useMemo, useState } from 'react';
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const monthOptions = [
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
const yearOptions = Array.from({ length: 6 }, (_, index) => String(currentYear - index));

const emptyBorrowing = {
  filter: { periode_label: '' },
  summary: { total_peminjaman: 0, jumlah_bulan_aktif: 0 },
  data: [],
  trend: [],
};

const emptyClassVisit = {
  filter: { periode_label: '' },
  summary: { total_kunjungan_valid: 0, total_kunjungan_tidak_valid: 0, jumlah_kelas_aktif: 0 },
  data: [],
};

const emptyDayVisit = {
  filter: { periode_label: '' },
  summary: { total_kunjungan: 0, hari_aktif: 0 },
  data: [],
};

const getMax = (rows, key) => Math.max(1, ...rows.map((item) => Number(item[key] || 0)));

const getLabel = (item, key) => item?.label || item?.[key] || '-';

const DashboardMetric = ({ label, value, tone = 'blue' }) => {
  const toneClass = {
    blue: 'border-[#265F9C]/20 bg-blue-50 text-[#265F9C]',
    green: 'border-[#2E7D32]/20 bg-green-50 text-[#2E7D32]',
    amber: 'border-[#EDA60F]/20 bg-amber-50 text-[#9A6600]',
  }[tone];

  return (
    <div className={`rounded-xl border p-4 ${toneClass}`}>
      <p className="text-[10px] font-black uppercase tracking-[0.18em]">{label}</p>
      <p className="mt-2 text-3xl font-bold font-montserrat text-[#1A1A1A]">
        {Number(value || 0).toLocaleString('id-ID')}
      </p>
    </div>
  );
};

const LineChart = ({ rows, labelKey = 'label', valueKey, strokeColor = '#265F9C' }) => {
  const chartRows = rows || [];
  const width = 420;
  const height = 172;
  const padding = { top: 20, right: 22, bottom: 30, left: 34 };
  const plotWidth = width - padding.left - padding.right;
  const plotHeight = height - padding.top - padding.bottom;
  const maxValue = getMax(chartRows, valueKey);

  if (chartRows.length === 0) {
    return (
      <div className="mt-5 rounded-xl border border-dashed border-gray-300 bg-white px-4 py-8 text-center text-xs font-bold text-[#7D7D7E]">
        Grafik belum memiliki data.
      </div>
    );
  }

  const points = chartRows.map((item, index) => {
    const value = Number(item[valueKey] || 0);
    const x = chartRows.length === 1
      ? padding.left + plotWidth / 2
      : padding.left + (index / (chartRows.length - 1)) * plotWidth;
    const y = padding.top + plotHeight - (value / maxValue) * plotHeight;

    return {
      x,
      y,
      value,
      label: getLabel(item, labelKey),
    };
  });

  const linePath = points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
  const baseY = padding.top + plotHeight;
  const areaPath = points.length > 1
    ? `${linePath} L ${points[points.length - 1].x} ${baseY} L ${points[0].x} ${baseY} Z`
    : '';
  const labelIndexes = points.length <= 3
    ? points.map((_, index) => index)
    : [0, Math.floor((points.length - 1) / 2), points.length - 1];

  return (
    <div className="mt-5 rounded-xl border border-gray-200 bg-white p-4">
      <div className="mb-3 flex items-center justify-between gap-3">
        <p className="text-[10px] font-black uppercase tracking-[0.18em] text-[#585858]">Grafik Garis</p>
        <p className="text-[10px] font-bold text-[#7D7D7E]">Maks. {maxValue.toLocaleString('id-ID')}</p>
      </div>
      <svg className="h-44 w-full overflow-visible" viewBox={`0 0 ${width} ${height}`} role="img" aria-label="Grafik garis laporan">
        {[0, 0.5, 1].map((ratio) => {
          const y = padding.top + plotHeight * ratio;
          return (
            <line
              key={ratio}
              x1={padding.left}
              x2={width - padding.right}
              y1={y}
              y2={y}
              stroke="#E5E7EB"
              strokeDasharray={ratio === 1 ? '0' : '4 4'}
            />
          );
        })}
        {areaPath && <path d={areaPath} fill={strokeColor} opacity="0.1" />}
        <path d={linePath} fill="none" stroke={strokeColor} strokeLinecap="round" strokeLinejoin="round" strokeWidth="4" />
        {points.map((point, index) => (
          <g key={`${point.label}-${index}`}>
            <title>{`${point.label}: ${point.value.toLocaleString('id-ID')}`}</title>
            <circle cx={point.x} cy={point.y} r="5" fill="white" stroke={strokeColor} strokeWidth="3" />
          </g>
        ))}
        <line x1={padding.left} x2={width - padding.right} y1={baseY} y2={baseY} stroke="#D1D5DB" />
      </svg>
      <div className="mt-1 flex justify-between gap-3 text-[10px] font-bold uppercase text-[#7D7D7E]">
        {labelIndexes.map((index) => (
          <span key={`${points[index].label}-${index}`} className="truncate">
            {points[index].label}
          </span>
        ))}
      </div>
    </div>
  );
};

const ReportTable = ({ columns, rows, emptyText = 'Belum ada data.' }) => (
  <div className="max-h-[280px] overflow-auto rounded-xl border border-gray-200 bg-white">
    <table className="w-full text-left">
      <thead className="sticky top-0 bg-gray-50 text-[10px] font-black uppercase tracking-[0.14em] text-[#585858]">
        <tr>
          {columns.map((column) => (
            <th key={column.key} className={`p-3 ${column.align === 'right' ? 'text-right' : column.align === 'center' ? 'text-center' : ''}`}>
              {column.header}
            </th>
          ))}
        </tr>
      </thead>
      <tbody className="divide-y divide-gray-100 text-sm">
        {rows.length > 0 ? rows.map((row, index) => (
          <tr key={row.key || index} className="hover:bg-blue-50/30">
            {columns.map((column) => (
              <td key={column.key} className={`p-3 ${column.align === 'right' ? 'text-right' : column.align === 'center' ? 'text-center' : ''}`}>
                {column.render ? column.render(row, index) : row[column.key]}
              </td>
            ))}
          </tr>
        )) : (
          <tr>
            <td colSpan={columns.length} className="p-8 text-center text-sm font-bold text-[#7D7D7E]">
              {emptyText}
            </td>
          </tr>
        )}
      </tbody>
    </table>
  </div>
);

const ReportSection = ({ title, description, actions = null, summary = null, chart, table }) => (
  <section className="rounded-xl border border-gray-200 bg-[#FAFAFA] p-5">
    <div className="mb-5 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
        <h3 className="font-bold font-montserrat text-[#1A1A1A]">{title}</h3>
        <p className="mt-1 text-xs text-[#585858]">{description}</p>
      </div>
      {actions}
    </div>
    {summary}
    <div className="grid grid-cols-1 gap-5 xl:grid-cols-[0.95fr,1.05fr] xl:items-start">
      <div>{chart}</div>
      <div>{table}</div>
    </div>
  </section>
);

const DashboardReportsPanel = () => {
  const [year, setYear] = useState(String(currentYear));
  const [month, setMonth] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [borrowing, setBorrowing] = useState(emptyBorrowing);
  const [classVisit, setClassVisit] = useState(emptyClassVisit);
  const [dayVisit, setDayVisit] = useState(emptyDayVisit);

  useEffect(() => {
    const fetchReports = async () => {
      setLoading(true);
      setError('');

      try {
        const borrowingParams = { tahun: year };
        if (month) {
          borrowingParams.bulan = month;
        }

        const [borrowingRes, classRes, dayRes] = await Promise.all([
          axios.get(`${API_BASE_URL}/laporan/peminjaman-bulanan`, { params: borrowingParams }),
          axios.get(`${API_BASE_URL}/laporan/kunjungan-distribusi-kelas`),
          axios.get(`${API_BASE_URL}/laporan/kunjungan-distribusi-hari`),
        ]);

        setBorrowing(borrowingRes.data || emptyBorrowing);
        setClassVisit(classRes.data || emptyClassVisit);
        setDayVisit(dayRes.data || emptyDayVisit);
      } catch (reportError) {
        console.error(reportError);
        setError('Laporan dashboard gagal dimuat. Pastikan API Laravel berjalan dan database aktif.');
      } finally {
        setLoading(false);
      }
    };

    fetchReports();
  }, [year, month]);

  const borrowingTrend = useMemo(() => {
    if (borrowing.trend?.length) {
      return borrowing.trend;
    }

    return borrowing.data.map((item) => ({
      label: item.nama_bulan,
      jumlah_peminjaman: item.jumlah_peminjaman,
    }));
  }, [borrowing.data, borrowing.trend]);
  const classTrend = useMemo(() => classVisit.data.map((item) => ({
    label: `Kelas ${item.kelas}`,
    jumlah_kunjungan: item.jumlah_kunjungan,
  })), [classVisit.data]);
  const dayTrend = useMemo(() => dayVisit.data.map((item) => ({
    label: item.hari,
    jumlah_kunjungan: item.jumlah_kunjungan,
  })), [dayVisit.data]);
  const borrowingTableRows = useMemo(() => {
    if (month) {
      return borrowingTrend.map((item, index) => ({
        key: item.nomor_hari || index,
        label: item.label,
        jumlah_peminjaman: item.jumlah_peminjaman,
      }));
    }

    return borrowing.data.map((item) => ({
      key: item.nomor_bulan,
      label: item.nama_bulan,
      jumlah_peminjaman: item.jumlah_peminjaman,
    }));
  }, [borrowing.data, borrowingTrend, month]);
  const classTableRows = useMemo(() => classVisit.data.map((item) => ({
    key: item.kelas,
    label: `Kelas ${item.kelas}`,
    jumlah_kunjungan: item.jumlah_kunjungan,
    persentase: item.persentase,
  })), [classVisit.data]);
  const dayTableRows = useMemo(() => dayVisit.data.map((item) => ({
    key: item.hari,
    label: item.hari,
    jumlah_kunjungan: item.jumlah_kunjungan,
    persentase: item.persentase,
  })), [dayVisit.data]);
  const periodeLabel = borrowing.filter?.periode_label || '-';
  const borrowingDescription = month
    ? 'Trend harian transaksi peminjaman pada bulan yang dipilih.'
    : 'Jumlah transaksi peminjaman per bulan.';
  const borrowingPeriodHeader = month ? 'Tanggal' : 'Bulan';
  const borrowingActivePeriod = month
    ? borrowingTrend.filter((item) => Number(item.jumlah_peminjaman || 0) > 0).length
    : borrowing.summary?.jumlah_bulan_aktif;
  const countBadge = (value, suffix = '') => (
    <span className="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-[#1A1A1A]">
      {Number(value || 0).toLocaleString('id-ID')}{suffix}
    </span>
  );
  const percentageText = (value) => `${Number(value || 0).toLocaleString('id-ID')}%`;

  return (
    <section className="mt-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <div className="mb-6">
        <div>
          <p className="text-xs font-black uppercase tracking-[0.25em] text-[#265F9C]">Laporan Dashboard</p>
          <h2 className="mt-2 text-2xl font-bold font-montserrat text-[#1A1A1A]">Ringkasan Peminjaman dan Kunjungan</h2>
          <p className="mt-2 text-sm text-[#585858]">Filter bulan dan tahun tersedia khusus untuk laporan statistik peminjaman buku bulanan.</p>
        </div>
      </div>

      {error && <div className="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">{error}</div>}

      {loading ? (
        <div className="rounded-xl border border-dashed border-gray-300 bg-gray-50 py-12 text-center text-sm font-bold text-[#585858]">
          Memuat laporan dashboard...
        </div>
      ) : (
        <div className="space-y-6">
          <ReportSection
            title="Statistik Peminjaman Buku"
            description={borrowingDescription}
            actions={(
              <div className="flex flex-wrap gap-3">
                <select className="rounded-xl border bg-white p-3 text-sm font-bold" value={month} onChange={(event) => setMonth(event.target.value)}>
                  {monthOptions.map((option) => <option key={option.value || 'all'} value={option.value}>{option.label}</option>)}
                </select>
                <select className="rounded-xl border bg-white p-3 text-sm font-bold" value={year} onChange={(event) => setYear(event.target.value)}>
                  {yearOptions.map((option) => <option key={option} value={option}>{option}</option>)}
                </select>
              </div>
            )}
            summary={(
              <div className="mb-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="rounded-xl border border-gray-200 bg-white p-4">
                  <p className="text-[10px] font-black uppercase tracking-[0.18em] text-[#585858]">Periode</p>
                  <p className="mt-2 text-xl font-bold font-montserrat text-[#1A1A1A]">{periodeLabel}</p>
                </div>
                <DashboardMetric label="Total Peminjaman" value={borrowing.summary?.total_peminjaman} tone="blue" />
                <DashboardMetric label={month ? 'Hari Aktif' : 'Bulan Aktif'} value={borrowingActivePeriod} tone="amber" />
              </div>
            )}
            chart={<LineChart rows={borrowingTrend} labelKey="label" valueKey="jumlah_peminjaman" strokeColor="#265F9C" />}
            table={(
              <div>
                <p className="mb-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#585858]">Tabel Data</p>
                <ReportTable
                  columns={[
                    { key: 'label', header: borrowingPeriodHeader, render: (row) => <span className="font-bold text-[#1A1A1A]">{row.label}</span> },
                    { key: 'jumlah_peminjaman', header: 'Jumlah Peminjaman', align: 'right', render: (row) => countBadge(row.jumlah_peminjaman, ' transaksi') },
                  ]}
                  rows={borrowingTableRows}
                  emptyText="Belum ada transaksi peminjaman pada periode ini."
                />
              </div>
            )}
          />

          <ReportSection
            title="Distribusi Kunjungan Kelas"
            description="Kelas diambil dari data siswa yang valid."
            summary={(
              <div className="mb-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                <DashboardMetric label="Kunjungan Valid" value={classVisit.summary?.total_kunjungan_valid} tone="green" />
                <DashboardMetric label="Kelas Aktif" value={classVisit.summary?.jumlah_kelas_aktif} tone="blue" />
                <DashboardMetric label="Tidak Valid" value={classVisit.summary?.total_kunjungan_tidak_valid} tone="amber" />
              </div>
            )}
            chart={<LineChart rows={classTrend} labelKey="label" valueKey="jumlah_kunjungan" strokeColor="#2E7D32" />}
            table={(
              <div>
                <p className="mb-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#585858]">Tabel Data</p>
                <ReportTable
                  columns={[
                    { key: 'label', header: 'Kelas', render: (row) => <span className="font-bold text-[#1A1A1A]">{row.label}</span> },
                    { key: 'jumlah_kunjungan', header: 'Jumlah', align: 'right', render: (row) => countBadge(row.jumlah_kunjungan, ' kunjungan') },
                    { key: 'persentase', header: 'Distribusi', align: 'right', render: (row) => <span className="font-bold text-[#2E7D32]">{percentageText(row.persentase)}</span> },
                  ]}
                  rows={classTableRows}
                  emptyText="Belum ada data kunjungan kelas."
                />
              </div>
            )}
          />

          <ReportSection
            title="Distribusi Kunjungan Hari"
            description="Dikelompokkan otomatis dari Senin sampai Minggu."
            summary={(
              <div className="mb-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <DashboardMetric label="Total Kunjungan" value={dayVisit.summary?.total_kunjungan} tone="green" />
                <DashboardMetric label="Hari Aktif" value={dayVisit.summary?.hari_aktif} tone="amber" />
              </div>
            )}
            chart={<LineChart rows={dayTrend} labelKey="label" valueKey="jumlah_kunjungan" strokeColor="#EDA60F" />}
            table={(
              <div>
                <p className="mb-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#585858]">Tabel Data</p>
                <ReportTable
                  columns={[
                    { key: 'label', header: 'Hari', render: (row) => <span className="font-bold text-[#1A1A1A]">{row.label}</span> },
                    { key: 'jumlah_kunjungan', header: 'Jumlah', align: 'right', render: (row) => countBadge(row.jumlah_kunjungan, ' kunjungan') },
                    { key: 'persentase', header: 'Distribusi', align: 'right', render: (row) => <span className="font-bold text-[#EDA60F]">{percentageText(row.persentase)}</span> },
                  ]}
                  rows={dayTableRows}
                  emptyText="Belum ada data kunjungan hari."
                />
              </div>
            )}
          />
        </div>
      )}
    </section>
  );
};

export default DashboardReportsPanel;
