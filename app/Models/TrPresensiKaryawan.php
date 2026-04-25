<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPresensiKaryawan
 * 
 * @property int $ID_PRESENSI_KARYAWAN
 * @property string|null $NIP_KARYAWAN
 * @property string|null $STATUS_PRESENSI_KARYAWAN
 * @property Carbon|null $WAKTU_MASUK
 * @property Carbon|null $WAKTU_KELUAR
 * 
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class TrPresensiKaryawan extends Model
{
	protected $table = 'tr_presensi_karyawan';
	protected $primaryKey = 'ID_PRESENSI_KARYAWAN';
	public $timestamps = false;

	protected $casts = [
		'WAKTU_MASUK' => 'datetime',
		'WAKTU_KELUAR' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'STATUS_PRESENSI_KARYAWAN',
		'WAKTU_MASUK',
		'WAKTU_KELUAR'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
