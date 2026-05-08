const pick = (source, keys, fallback = '-') => {
    for (const key of keys) {
        const value = source?.[key];

        if (value !== undefined && value !== null && String(value).trim() !== '') {
            return value;
        }
    }

    return fallback;
};

const formatDate = (value) => {
    if (!value) return '-';

    const date = new Date(String(value).replace(' ', 'T'));

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
};

const getProfileType = (user) => (
    user?.NISN_SISWA || user?.nisn_siswa || user?.NAMA_SISWA_TETAP || user?.nama_siswa_tetap
        ? 'siswa'
        : 'karyawan'
);

const ProfileField = ({ label, value }) => (
    <div className="rounded-lg border border-slate-100 bg-slate-50 p-3">
        <p className="text-[9px] font-black uppercase tracking-widest text-[#7D7D7E]">{label}</p>
        <p className="mt-1.5 break-words text-xs font-bold text-[#1A1A1A]">{value || '-'}</p>
    </div>
);

const ProfilePanel = ({ user, onLogout, context = 'member' }) => {
    const type = getProfileType(user);
    const isSiswa = type === 'siswa';
    const displayName = isSiswa
        ? pick(user, ['NAMA_SISWA_TETAP', 'nama_siswa_tetap', 'nama'], 'Anggota')
        : pick(user, ['NAMA_KARYAWAN', 'nama_karyawan', 'NAMA_LENGKAP_GELAR', 'nama_lengkap_gelar'], 'Admin');
    const identifier = isSiswa
        ? pick(user, ['NISN_SISWA', 'nisn_siswa'])
        : pick(user, ['NIP_KARYAWAN', 'nip_karyawan']);
    const roleLabel = pick(
        user,
        ['ROLE_LABEL', 'JABATAN_FUNGSIONAL', 'jabatan_fungsional'],
        isSiswa ? 'Siswa' : 'Karyawan'
    );

    const fields = isSiswa
        ? [
            ['NISN', identifier],
            ['Nama Lengkap', displayName],
            ['Jenis Kelamin', pick(user, ['GENDER_SISWA', 'gender_siswa'])],
            ['Tempat Lahir', pick(user, ['TEMPAT_LAHIR_SISWA', 'tempat_lahir_siswa'])],
            ['Tanggal Lahir', formatDate(pick(user, ['TGL_LAHIR_SISWA', 'tgl_lahir_siswa'], ''))],
            ['No. HP', pick(user, ['NO_HP_SISWA', 'no_hp_siswa'])],
            ['Alamat', pick(user, ['ALAMAT_JALAN_SISWA', 'alamat_jalan_siswa'])],
            ['Kota/Kabupaten', pick(user, ['KOTA_KAB_SISWA', 'kota_kab_siswa'])],
            ['Provinsi', pick(user, ['PROVINSI_SISWA', 'provinsi_siswa'])],
            ['Tahun Lulus', pick(user, ['TAHUN_LULUS', 'tahun_lulus'])],
        ]
        : [
            ['NIP', identifier],
            ['Nama Lengkap', pick(user, ['NAMA_LENGKAP_GELAR', 'nama_lengkap_gelar'], displayName)],
            ['Nama Panggilan', displayName],
            ['Jabatan', pick(user, ['JABATAN_FUNGSIONAL', 'jabatan_fungsional'])],
            ['Status Kepegawaian', pick(user, ['STATUS_KEPEGAWAIAN', 'status_kepegawaian'])],
            ['Golongan', pick(user, ['GOLONGAN_KARYAWAN', 'golongan_karyawan'])],
            ['Jenis Kelamin', pick(user, ['GENDER_KARYAWAN', 'gender_karyawan'])],
            ['Tempat Lahir', pick(user, ['TEMPAT_LAHIR_KARYAWAN', 'tempat_lahir_karyawan'])],
            ['Tanggal Lahir', formatDate(pick(user, ['TGL_LAHIR_KARYAWAN', 'tgl_lahir_karyawan'], ''))],
            ['No. HP', pick(user, ['NO_HP_KARYAWAN', 'no_hp_karyawan'])],
            ['Email', pick(user, ['EMAIL_KARYAWAN', 'email_karyawan'])],
            ['Alamat', pick(user, ['ALAMAT_KARYAWAN', 'alamat_karyawan'])],
            ['Pendidikan Terakhir', pick(user, ['PEND_TERAKHIR_KARYAWAN', 'pend_terakhir_karyawan'])],
            ['Prodi', pick(user, ['PRODI_KARYAWAN', 'prodi_karyawan'])],
        ];

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div className="flex flex-col gap-3 border-b border-slate-100 pb-4 md:flex-row md:items-center md:justify-between">
                <div className="min-w-0">
                    <p className="text-[9px] font-black uppercase tracking-[0.2em] text-[#7D7D7E]">
                        {context === 'admin' ? 'Profil Admin' : 'Profil Anggota'}
                    </p>
                    <h1 className="mt-1 truncate text-lg font-bold font-montserrat text-[#1A1A1A]">{displayName}</h1>
                    <p className="mt-0.5 truncate text-xs font-semibold text-[#265F9C]">{roleLabel} | {identifier}</p>
                </div>

                {onLogout && (
                    <button
                        type="button"
                        onClick={onLogout}
                        className="w-fit rounded-lg bg-[#C62828] px-3 py-2 text-[11px] font-bold text-white shadow-sm transition-colors hover:bg-red-800"
                    >
                        Keluar Akun
                    </button>
                )}
            </div>

            <div className="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                {fields.map(([label, value]) => (
                    <ProfileField key={label} label={label} value={value} />
                ))}
            </div>
        </div>
    );
};

export default ProfilePanel;
