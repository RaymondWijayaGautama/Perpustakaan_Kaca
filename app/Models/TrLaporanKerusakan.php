<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrLaporanKerusakan
 * 
 * @property int $ID_TR_LAPORAN
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_LAPORAN_KERUSAKAN
 * @property string|null $KETERANGAN_LAPORAN
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|MstInventari[] $mst_inventaris
 *
 * @package App\Models
 */
class TrLaporanKerusakan extends Model
{
	protected $table = 'tr_laporan_kerusakan';
	protected $primaryKey = 'ID_TR_LAPORAN';
	public $timestamps = false;

	protected $casts = [
		'TGL_LAPORAN_KERUSAKAN' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'TGL_LAPORAN_KERUSAKAN',
		'KETERANGAN_LAPORAN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function mst_inventaris()
	{
		return $this->hasMany(MstInventari::class, 'ID_TR_LAPORAN');
	}
}
