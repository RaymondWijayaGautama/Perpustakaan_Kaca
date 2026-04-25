<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJurnalKaryawan
 * 
 * @property int $ID_TR_JURNAL_KARYAWAN
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KETUGASAN_KARYAWAN
 * @property string|null $MINGGU_KARYAWAN
 * @property Carbon|null $TGL_PENYERAHAN_KARYAWAN
 * @property string|null $NIP_VALIDATOR_KARYAWAN
 * @property string|null $STATUS_J_KARYAWAN
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtJurnalKaryawan[] $dt_jurnal_karyawans
 *
 * @package App\Models
 */
class TrJurnalKaryawan extends Model
{
	protected $table = 'tr_jurnal_karyawan';
	protected $primaryKey = 'ID_TR_JURNAL_KARYAWAN';
	public $timestamps = false;

	protected $casts = [
		'TGL_PENYERAHAN_KARYAWAN' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'KETUGASAN_KARYAWAN',
		'MINGGU_KARYAWAN',
		'TGL_PENYERAHAN_KARYAWAN',
		'NIP_VALIDATOR_KARYAWAN',
		'STATUS_J_KARYAWAN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_jurnal_karyawans()
	{
		return $this->hasMany(DtJurnalKaryawan::class, 'ID_TR_JURNAL_KARYAWAN');
	}
}
