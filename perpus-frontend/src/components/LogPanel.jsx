import { useEffect, useMemo, useState } from 'react';
import axios from 'axios';
import {
  ArrowPathIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  MagnifyingGlassIcon,
} from '@heroicons/react/24/outline';

const API_BASE = 'http://localhost:8000/api';

const emptyPager = {
  data: [],
  current_page: 1,
  last_page: 1,
  total: 0,
};

const formatDateTime = (value) => {
  if (!value) return '-';

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));
};

const formatDuration = (start, end) => {
  if (!start) return '-';
  if (!end) return 'Aktif';

  const diffMs = new Date(end).getTime() - new Date(start).getTime();
  if (Number.isNaN(diffMs) || diffMs < 0) return '-';

  const minutes = Math.floor(diffMs / 60000);
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;

  if (hours > 0) return `${hours} jam ${remainingMinutes} menit`;
  return `${Math.max(minutes, 1)} menit`;
};

const LogPanel = () => {
  const [activeLog, setActiveLog] = useState('access');
  const [filters, setFilters] = useState({
    search: '',
    role: '',
    date_from: '',
    date_to: '',
  });
  const [page, setPage] = useState(1);
  const [accessLogs, setAccessLogs] = useState(emptyPager);
  const [activityLogs, setActivityLogs] = useState(emptyPager);
  const [loading, setLoading] = useState(false);

  const pager = activeLog === 'access' ? accessLogs : activityLogs;

  const params = useMemo(() => ({
    page,
    per_page: 12,
    search: filters.search || undefined,
    role: filters.role || undefined,
    date_from: filters.date_from || undefined,
    date_to: filters.date_to || undefined,
  }), [filters, page]);

  useEffect(() => {
    const fetchLogs = async () => {
      setLoading(true);

      try {
        const endpoint = activeLog === 'access' ? '/logs/access' : '/logs/activity';
        const response = await axios.get(`${API_BASE}${endpoint}`, { params });

        if (activeLog === 'access') {
          setAccessLogs(response.data);
        } else {
          setActivityLogs(response.data);
        }
      } catch (error) {
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchLogs();
  }, [activeLog, params]);

  const updateFilter = (key, value) => {
    setFilters((current) => ({ ...current, [key]: value }));
    setPage(1);
  };

  const resetFilters = () => {
    setFilters({ search: '', role: '', date_from: '', date_to: '' });
    setPage(1);
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <h1 className="font-montserrat text-2xl font-bold text-[#1A1A1A]">Log Sistem</h1>
          <p className="mt-1 text-sm font-medium text-[#585858]">
            {pager.total.toLocaleString('id-ID')} catatan ditemukan
          </p>
        </div>

        <div className="flex flex-wrap gap-3">
          <div className="flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <button
              type="button"
              onClick={() => { setActiveLog('access'); setPage(1); }}
              className={`px-4 py-2 text-sm font-bold transition-colors ${activeLog === 'access' ? 'bg-[#265F9C] text-white' : 'text-slate-600 hover:bg-slate-50'}`}
            >
              Access Log
            </button>
            <button
              type="button"
              onClick={() => { setActiveLog('activity'); setPage(1); }}
              className={`px-4 py-2 text-sm font-bold transition-colors ${activeLog === 'activity' ? 'bg-[#265F9C] text-white' : 'text-slate-600 hover:bg-slate-50'}`}
            >
              Activity Log
            </button>
          </div>
          <button
            type="button"
            onClick={resetFilters}
            title="Reset filter"
            className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition-colors hover:bg-slate-50"
          >
            <ArrowPathIcon className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
        <label className="relative block">
          <MagnifyingGlassIcon className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
          <input
            type="text"
            value={filters.search}
            onChange={(event) => updateFilter('search', event.target.value)}
            placeholder="Cari username, aktivitas, endpoint..."
            className="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-3 text-sm font-medium outline-none transition focus:border-[#265F9C] focus:bg-white focus:ring-2 focus:ring-[#265F9C]/20"
          />
        </label>

        <select
          value={filters.role}
          onChange={(event) => updateFilter('role', event.target.value)}
          className="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-700 outline-none transition focus:border-[#265F9C] focus:bg-white focus:ring-2 focus:ring-[#265F9C]/20"
        >
          <option value="">Semua Role</option>
          <option value="Pustakawan">Pustakawan</option>
          <option value="Admin">Admin</option>
          <option value="Siswa">Siswa</option>
          <option value="Karyawan">Karyawan</option>
          <option value="Guru">Guru</option>
        </select>

        <input
          type="date"
          value={filters.date_from}
          onChange={(event) => updateFilter('date_from', event.target.value)}
          className="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-700 outline-none transition focus:border-[#265F9C] focus:bg-white focus:ring-2 focus:ring-[#265F9C]/20"
        />

        <input
          type="date"
          value={filters.date_to}
          onChange={(event) => updateFilter('date_to', event.target.value)}
          className="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-700 outline-none transition focus:border-[#265F9C] focus:bg-white focus:ring-2 focus:ring-[#265F9C]/20"
        />
      </div>

      <div className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        {loading ? (
          <div className="flex h-64 items-center justify-center">
            <div className="h-10 w-10 animate-spin rounded-full border-4 border-[#265F9C] border-t-transparent" />
          </div>
        ) : activeLog === 'access' ? (
          <AccessLogTable rows={accessLogs.data || []} />
        ) : (
          <ActivityLogTable rows={activityLogs.data || []} />
        )}
      </div>

      <div className="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <button
          type="button"
          disabled={page <= 1}
          onClick={() => setPage((current) => Math.max(current - 1, 1))}
          title="Halaman sebelumnya"
          className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        >
          <ChevronLeftIcon className="h-5 w-5" />
        </button>
        <span className="text-xs font-black uppercase text-[#585858]">
          Halaman {pager.current_page || 1} / {pager.last_page || 1}
        </span>
        <button
          type="button"
          disabled={page >= (pager.last_page || 1)}
          onClick={() => setPage((current) => current + 1)}
          title="Halaman berikutnya"
          className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        >
          <ChevronRightIcon className="h-5 w-5" />
        </button>
      </div>
    </div>
  );
};

const AccessLogTable = ({ rows }) => (
  <div className="overflow-x-auto">
    <table className="w-full min-w-[860px] text-left">
      <thead className="border-b bg-slate-50 text-[11px] font-black uppercase text-[#585858]">
        <tr>
          <th className="px-4 py-3">ID</th>
          <th className="px-4 py-3">Username</th>
          <th className="px-4 py-3">Role</th>
          <th className="px-4 py-3">Mulai Login</th>
          <th className="px-4 py-3">Selesai Login</th>
          <th className="px-4 py-3">Durasi</th>
        </tr>
      </thead>
      <tbody className="divide-y divide-slate-100 text-sm">
        {rows.length === 0 ? (
          <tr>
            <td colSpan="6" className="px-4 py-12 text-center font-bold text-slate-400">Belum ada access log.</td>
          </tr>
        ) : rows.map((row) => (
          <tr key={row.id} className="hover:bg-blue-50/40">
            <td className="px-4 py-3 font-mono text-xs text-slate-500">#{row.id}</td>
            <td className="px-4 py-3 font-bold text-slate-900">{row.username || '-'}</td>
            <td className="px-4 py-3">
              <span className="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-black text-[#265F9C]">
                {row.role || 'Unknown'}
              </span>
            </td>
            <td className="px-4 py-3 text-slate-700">{formatDateTime(row.start_login)}</td>
            <td className="px-4 py-3 text-slate-700">{formatDateTime(row.end_login)}</td>
            <td className="px-4 py-3 font-bold text-slate-700">{formatDuration(row.start_login, row.end_login)}</td>
          </tr>
        ))}
      </tbody>
    </table>
  </div>
);

const ActivityLogTable = ({ rows }) => (
  <div className="overflow-x-auto">
    <table className="w-full min-w-[1040px] text-left">
      <thead className="border-b bg-slate-50 text-[11px] font-black uppercase text-[#585858]">
        <tr>
          <th className="px-4 py-3">Waktu</th>
          <th className="px-4 py-3">Aktor</th>
          <th className="px-4 py-3">Role</th>
          <th className="px-4 py-3">Aktivitas</th>
          <th className="px-4 py-3">Data Terkait</th>
          <th className="px-4 py-3">Deskripsi</th>
        </tr>
      </thead>
      <tbody className="divide-y divide-slate-100 text-sm">
        {rows.length === 0 ? (
          <tr>
            <td colSpan="6" className="px-4 py-12 text-center font-bold text-slate-400">Belum ada activity log.</td>
          </tr>
        ) : rows.map((row) => (
          <tr key={row.id} className="align-top hover:bg-blue-50/40">
            <td className="whitespace-nowrap px-4 py-3 text-slate-700">{formatDateTime(row.event_time)}</td>
            <td className="px-4 py-3 font-bold text-slate-900">{row.actor_username || '-'}</td>
            <td className="px-4 py-3">
              <span className="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-[11px] font-black text-emerald-700">
                {row.actor_role || 'Unknown'}
              </span>
            </td>
            <td className="px-4 py-3 font-mono text-xs font-bold text-[#265F9C]">{row.activity_name || '-'}</td>
            <td className="px-4 py-3 font-mono text-xs text-slate-600">{row.related_data || '-'}</td>
            <td className="max-w-[420px] px-4 py-3 text-slate-700">{row.activity_description || '-'}</td>
          </tr>
        ))}
      </tbody>
    </table>
  </div>
);

export default LogPanel;
