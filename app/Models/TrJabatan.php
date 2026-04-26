<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJabatan
 * 
 * @property int $ID_TR_JABATAN
 * @property int|null $ID_JABATAN
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_MULAI_JABATAN
 * @property Carbon|null $TGL_SELESAI_JABATAN
 * @property string|null $NO_SK_JABATAN
 * 
 * @property RefJabatanStr|null $ref_jabatan_str
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class TrJabatan extends Model
{
	protected $table = 'tr_jabatan';
	protected $primaryKey = 'ID_TR_JABATAN';
	public $timestamps = false;

	protected $casts = [
		'ID_JABATAN' => 'int',
		'TGL_MULAI_JABATAN' => 'datetime',
		'TGL_SELESAI_JABATAN' => 'datetime'
	];

	protected $fillable = [
		'ID_JABATAN',
		'NIP_KARYAWAN',
		'TGL_MULAI_JABATAN',
		'TGL_SELESAI_JABATAN',
		'NO_SK_JABATAN'
	];

	public function ref_jabatan_str()
	{
		return $this->belongsTo(RefJabatanStr::class, 'ID_JABATAN');
	}

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
