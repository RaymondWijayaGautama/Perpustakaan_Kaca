<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefTum
 * 
 * @property int $KODE_TA
 * @property string|null $TA
 * @property string|null $SEMESTER
 * @property bool|null $IS_CURRENT
 * 
 * @property Collection|HstKela[] $hst_kelas
 * @property Collection|MstCalonSiswa[] $mst_calon_siswas
 * @property Collection|MstSiswa[] $mst_siswas
 * @property Collection|PendafPkl[] $pendaf_pkls
 * @property Collection|SiswaKela[] $siswa_kelas
 * @property Collection|TrPembayaran[] $tr_pembayarans
 * @property Collection|TrPendaftaran[] $tr_pendaftarans
 * @property Collection|TrPkg[] $tr_pkgs
 * @property Collection|TrPrestasiPelanggaranSiswa[] $tr_prestasi_pelanggaran_siswas
 *
 * @package App\Models
 */
class RefTum extends Model
{
	protected $table = 'ref_ta';
	protected $primaryKey = 'KODE_TA';
	public $timestamps = false;

	protected $casts = [
		'IS_CURRENT' => 'bool'
	];

	protected $fillable = [
		'TA',
		'SEMESTER',
		'IS_CURRENT'
	];

	public function hst_kelas()
	{
		return $this->hasMany(HstKela::class, 'KODE_TA');
	}

	public function mst_calon_siswas()
	{
		return $this->hasMany(MstCalonSiswa::class, 'KODE_TA');
	}

	public function mst_siswas()
	{
		return $this->hasMany(MstSiswa::class, 'KODE_TA');
	}

	public function pendaf_pkls()
	{
		return $this->hasMany(PendafPkl::class, 'KODE_TA');
	}

	public function siswa_kelas()
	{
		return $this->hasMany(SiswaKela::class, 'KODE_TA');
	}

	public function tr_pembayarans()
	{
		return $this->hasMany(TrPembayaran::class, 'KODE_TA');
	}

	public function tr_pendaftarans()
	{
		return $this->hasMany(TrPendaftaran::class, 'KODE_TA');
	}

	public function tr_pkgs()
	{
		return $this->hasMany(TrPkg::class, 'KODE_TA');
	}

	public function tr_prestasi_pelanggaran_siswas()
	{
		return $this->hasMany(TrPrestasiPelanggaranSiswa::class, 'KODE_TA');
	}
}
