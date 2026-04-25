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
 * Class MstSiswa
 * 
 * @property int $ID_SISWA_TETAP
 * @property int|null $ID_PENDAFTARAN
 * @property int|null $KODE_TA
 * @property string|null $KODE_CALON_SISWA
 * @property string|null $NISN_SISWA
 * @property string|null $NAMA_SISWA_TETAP
 * @property Carbon|null $TGL_LAHIR_SISWA
 * @property string|null $TEMPAT_LAHIR_SISWA
 * @property string|null $GENDER_SISWA
 * @property string|null $GOLDAR_SISWA
 * @property string|null $NO_HP_SISWA
 * @property string|null $ALAMAT_JALAN_SISWA
 * @property string|null $RT_SISWA
 * @property string|null $RW_SISWA
 * @property string|null $KELURAHAN_SISWA
 * @property string|null $KECAMATAN_SISWA
 * @property string|null $KOTA_KAB_SISWA
 * @property string|null $PROVINSI_SISWA
 * @property string|null $KODE_POS_SISWA
 * @property string|null $NIK_SISWA
 * @property string|null $TAHUN_LULUS
 * @property string|null $PASSWORD_SISWA
 * @property string|null $NAMA_AYAH_SISWA
 * @property string|null $NAMA_IBU_SISWA
 * @property string|null $NAMA_WALI_SISWA
 * @property string|null $PEKERJAAN_AYAH_SISWA
 * @property string|null $PEKERJAAN_IBU_SISWA
 * @property string|null $PEKERJAAN_WALI_SISWA
 * @property bool|null $IS_DELETE
 * 
 * @property TrPendaftaran|null $tr_pendaftaran
 * @property RefTum|null $ref_tum
 * @property Collection|HstKela[] $hst_kelas
 * @property Collection|JadwalCoffeeshop[] $jadwal_coffeeshops
 * @property Collection|NilaiKinerjaCoffeeshop[] $nilai_kinerja_coffeeshops
 * @property Collection|PklSiswa[] $pkl_siswas
 * @property Collection|RaporIntegrita[] $rapor_integritas
 * @property Collection|SiswaKela[] $siswa_kelas
 * @property Collection|TagihanSiswa[] $tagihan_siswas
 * @property Collection|TrKunjunganPerpu[] $tr_kunjungan_perpus
 * @property Collection|TrPembayaran[] $tr_pembayarans
 * @property Collection|TrPeminjaman[] $tr_peminjamen
 * @property Collection|TrTracer[] $tr_tracers
 *
 * @package App\Models
 */
class MstSiswa extends Model
{

	use HasApiTokens;

	protected $table = 'mst_siswa';
	protected $primaryKey = 'ID_SISWA_TETAP';
	public $timestamps = false;

	protected $casts = [
		'ID_PENDAFTARAN' => 'int',
		'KODE_TA' => 'int',
		'TGL_LAHIR_SISWA' => 'datetime',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_PENDAFTARAN',
		'KODE_TA',
		'KODE_CALON_SISWA',
		'NISN_SISWA',
		'NAMA_SISWA_TETAP',
		'TGL_LAHIR_SISWA',
		'TEMPAT_LAHIR_SISWA',
		'GENDER_SISWA',
		'GOLDAR_SISWA',
		'NO_HP_SISWA',
		'ALAMAT_JALAN_SISWA',
		'RT_SISWA',
		'RW_SISWA',
		'KELURAHAN_SISWA',
		'KECAMATAN_SISWA',
		'KOTA_KAB_SISWA',
		'PROVINSI_SISWA',
		'KODE_POS_SISWA',
		'NIK_SISWA',
		'TAHUN_LULUS',
		'PASSWORD_SISWA',
		'NAMA_AYAH_SISWA',
		'NAMA_IBU_SISWA',
		'NAMA_WALI_SISWA',
		'PEKERJAAN_AYAH_SISWA',
		'PEKERJAAN_IBU_SISWA',
		'PEKERJAAN_WALI_SISWA',
		'IS_DELETE'
	];

	public function tr_pendaftaran()
	{
		return $this->belongsTo(TrPendaftaran::class, 'ID_PENDAFTARAN');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function hst_kelas()
	{
		return $this->hasMany(HstKela::class, 'ID_SISWA_TETAP');
	}

	public function jadwal_coffeeshops()
	{
		return $this->hasMany(JadwalCoffeeshop::class, 'ID_SISWA_TETAP');
	}

	public function nilai_kinerja_coffeeshops()
	{
		return $this->hasMany(NilaiKinerjaCoffeeshop::class, 'ID_SISWA_TETAP');
	}

	public function pkl_siswas()
	{
		return $this->hasMany(PklSiswa::class, 'ID_SISWA_TETAP');
	}

	public function rapor_integritas()
	{
		return $this->hasMany(RaporIntegrita::class, 'ID_SISWA_TETAP');
	}

	public function siswa_kelas()
	{
		return $this->hasMany(SiswaKela::class, 'ID_SISWA_TETAP');
	}

	public function tagihan_siswas()
	{
		return $this->hasMany(TagihanSiswa::class, 'ID_SISWA_TETAP');
	}

	public function tr_kunjungan_perpus()
	{
		return $this->hasMany(TrKunjunganPerpu::class, 'ID_SISWA_TETAP');
	}

	public function tr_pembayarans()
	{
		return $this->hasMany(TrPembayaran::class, 'ID_SISWA_TETAP');
	}

	public function tr_peminjamen()
	{
		return $this->hasMany(TrPeminjaman::class, 'ID_SISWA_TETAP');
	}

	public function tr_tracers()
	{
		return $this->hasMany(TrTracer::class, 'ID_SISWA_TETAP');
	}
}
