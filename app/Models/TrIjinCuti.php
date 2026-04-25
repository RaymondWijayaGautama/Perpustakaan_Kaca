<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrIjinCuti
 * 
 * @property int $ID_TR_IC
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_AWAL
 * @property Carbon|null $TGL_SELESAI
 * @property string|null $KETERANGAN_IJIN_CUTI
 * @property string|null $LINK_BUKTI_IJIN_CUTI
 * @property string|null $STATUS_IJIN_CUTI
 * 
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class TrIjinCuti extends Model
{
	protected $table = 'tr_ijin_cuti';
	protected $primaryKey = 'ID_TR_IC';
	public $timestamps = false;

	protected $casts = [
		'TGL_AWAL' => 'datetime',
		'TGL_SELESAI' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'TGL_AWAL',
		'TGL_SELESAI',
		'KETERANGAN_IJIN_CUTI',
		'LINK_BUKTI_IJIN_CUTI',
		'STATUS_IJIN_CUTI'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
