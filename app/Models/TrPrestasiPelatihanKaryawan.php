<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPrestasiPelatihanKaryawan
 * 
 * @property int $ID_PRESTASI_PELATIHAN
 * @property string|null $NIP_KARYAWAN
 * @property string|null $JENIS_PRESTASI_PELATIHAN
 * @property string|null $NAMA_PRETASI_PELATIHAN
 * @property string|null $TEMPAT_PRESTASI_PELATIHAN
 * @property Carbon|null $TGL_PRESTASI_PELATIHAN
 * @property string|null $KET_PRESTASI_PELATIHAN
 * @property Carbon|null $TGL_LAPOR
 * @property string|null $STATUS_PRESTASI_PELATIHAN
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DokumenPrestasiPelatihan[] $dokumen_prestasi_pelatihans
 *
 * @package App\Models
 */
class TrPrestasiPelatihanKaryawan extends Model
{
	protected $table = 'tr_prestasi_pelatihan_karyawan';
	protected $primaryKey = 'ID_PRESTASI_PELATIHAN';
	public $timestamps = false;

	protected $casts = [
		'TGL_PRESTASI_PELATIHAN' => 'datetime',
		'TGL_LAPOR' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'JENIS_PRESTASI_PELATIHAN',
		'NAMA_PRETASI_PELATIHAN',
		'TEMPAT_PRESTASI_PELATIHAN',
		'TGL_PRESTASI_PELATIHAN',
		'KET_PRESTASI_PELATIHAN',
		'TGL_LAPOR',
		'STATUS_PRESTASI_PELATIHAN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dokumen_prestasi_pelatihans()
	{
		return $this->hasMany(DokumenPrestasiPelatihan::class, 'ID_PRESTASI_PELATIHAN');
	}
}
