<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class MstKaryawan
 * 
 * @property string $NIP_KARYAWAN
 * @property int|null $ID_UNIT
 * @property string|null $NAMA_KARYAWAN
 * @property string|null $NAMA_LENGKAP_GELAR
 * @property string|null $GOLONGAN_KARYAWAN
 * @property string|null $JABATAN_FUNGSIONAL
 * @property Carbon|null $TANGGAL_MASUK
 * @property string|null $STATUS_KEPEGAWAIAN
 * @property string|null $NIK_KARYAWAN
 * @property string|null $TEMPAT_LAHIR_KARYAWAN
 * @property string|null $GENDER_KARYAWAN
 * @property Carbon|null $TGL_LAHIR_KARYAWAN
 * @property string|null $ALAMAT_KARYAWAN
 * @property string|null $NO_HP_KARYAWAN
 * @property string|null $EMAIL_KARYAWAN
 * @property string|null $PASSWORD_KARYAWAN
 * @property string|null $PEND_TERAKHIR_KARYAWAN
 * @property string|null $PRODI_KARYAWAN
 * @property string|null $SERTIFIKAT_PENDIDIK
 * @property string|null $LINK_FOTO_KARYAWAN
 * @property bool|null $IS_DELETE
 * 
 * @property MstUnit|null $mst_unit
 * @property Collection|DokumenKaryawan[] $dokumen_karyawans
 * @property Collection|GuruMapel[] $guru_mapels
 * @property Collection|JadwalCoffeeshop[] $jadwal_coffeeshops
 * @property Collection|MstUnit[] $mst_units
 * @property Collection|PklSiswa[] $pkl_siswas
 * @property Collection|TrEvaluasiMandiri[] $tr_evaluasi_mandiris
 * @property Collection|TrIjinCuti[] $tr_ijin_cutis
 * @property Collection|TrJabatan[] $tr_jabatans
 * @property Collection|TrJadwal[] $tr_jadwals
 * @property Collection|TrJurnalKaryawan[] $tr_jurnal_karyawans
 * @property Collection|TrJurnalManajeman[] $tr_jurnal_manajemen
 * @property Collection|TrJurnalMengajar[] $tr_jurnal_mengajars
 * @property Collection|TrJurnalWalikela[] $tr_jurnal_walikelas
 * @property Collection|TrLaporanKerusakan[] $tr_laporan_kerusakans
 * @property Collection|TrPeminjaman[] $tr_peminjamen
 * @property Collection|TrPeminjamanInventari[] $tr_peminjaman_inventaris
 * @property Collection|TrPkg[] $tr_pkgs
 * @property Collection|TrPresensiKaryawan[] $tr_presensi_karyawans
 * @property Collection|TrPrestasiPelatihanKaryawan[] $tr_prestasi_pelatihan_karyawans
 * @property Collection|TrWawancara[] $tr_wawancaras
 *
 * @package App\Models
 */
class MstKaryawan extends Model
{
	use HasApiTokens;
	
	protected $table = 'mst_karyawan';
	protected $primaryKey = 'NIP_KARYAWAN';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID_UNIT' => 'int',
		'TANGGAL_MASUK' => 'datetime',
		'TGL_LAHIR_KARYAWAN' => 'datetime',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_UNIT',
		'NAMA_KARYAWAN',
		'NAMA_LENGKAP_GELAR',
		'GOLONGAN_KARYAWAN',
		'JABATAN_FUNGSIONAL',
		'TANGGAL_MASUK',
		'STATUS_KEPEGAWAIAN',
		'NIK_KARYAWAN',
		'TEMPAT_LAHIR_KARYAWAN',
		'GENDER_KARYAWAN',
		'TGL_LAHIR_KARYAWAN',
		'ALAMAT_KARYAWAN',
		'NO_HP_KARYAWAN',
		'EMAIL_KARYAWAN',
		'PASSWORD_KARYAWAN',
		'PEND_TERAKHIR_KARYAWAN',
		'PRODI_KARYAWAN',
		'SERTIFIKAT_PENDIDIK',
		'LINK_FOTO_KARYAWAN',
		'IS_DELETE'
	];

	public function mst_unit()
	{
		return $this->belongsTo(MstUnit::class, 'ID_UNIT');
	}

	public function dokumen_karyawans()
	{
		return $this->hasMany(DokumenKaryawan::class, 'NIP_KARYAWAN');
	}

	public function guru_mapels()
	{
		return $this->hasMany(GuruMapel::class, 'NIP_KARYAWAN');
	}

	public function jadwal_coffeeshops()
	{
		return $this->hasMany(JadwalCoffeeshop::class, 'NIP_KARYAWAN');
	}

	public function mst_units()
	{
		return $this->hasMany(MstUnit::class, 'NIP_KARYAWAN');
	}

	public function pkl_siswas()
	{
		return $this->hasMany(PklSiswa::class, 'NIP_KARYAWAN');
	}

	public function tr_evaluasi_mandiris()
	{
		return $this->hasMany(TrEvaluasiMandiri::class, 'NIP_KARYAWAN');
	}

	public function tr_ijin_cutis()
	{
		return $this->hasMany(TrIjinCuti::class, 'NIP_KARYAWAN');
	}

	public function tr_jabatans()
	{
		return $this->hasMany(TrJabatan::class, 'NIP_KARYAWAN');
	}

	public function tr_jadwals()
	{
		return $this->hasMany(TrJadwal::class, 'NIP_KARYAWAN');
	}

	public function tr_jurnal_karyawans()
	{
		return $this->hasMany(TrJurnalKaryawan::class, 'NIP_KARYAWAN');
	}

	public function tr_jurnal_manajemen()
	{
		return $this->hasMany(TrJurnalManajeman::class, 'NIP_KARYAWAN');
	}

	public function tr_jurnal_mengajars()
	{
		return $this->hasMany(TrJurnalMengajar::class, 'NIP_KARYAWAN');
	}

	public function tr_jurnal_walikelas()
	{
		return $this->hasMany(TrJurnalWalikela::class, 'NIP_KARYAWAN');
	}

	public function tr_laporan_kerusakans()
	{
		return $this->hasMany(TrLaporanKerusakan::class, 'NIP_KARYAWAN');
	}

	public function tr_peminjamen()
	{
		return $this->hasMany(TrPeminjaman::class, 'NIP_KARYAWAN');
	}

	public function tr_peminjaman_inventaris()
	{
		return $this->hasMany(TrPeminjamanInventari::class, 'NIP_KARYAWAN');
	}

	public function tr_pkgs()
	{
		return $this->hasMany(TrPkg::class, 'NIP_KARYAWAN');
	}

	public function tr_presensi_karyawans()
	{
		return $this->hasMany(TrPresensiKaryawan::class, 'NIP_KARYAWAN');
	}

	public function tr_prestasi_pelatihan_karyawans()
	{
		return $this->hasMany(TrPrestasiPelatihanKaryawan::class, 'NIP_KARYAWAN');
	}

	public function tr_wawancaras()
	{
		return $this->hasMany(TrWawancara::class, 'NIP_KARYAWAN');
	}
}
