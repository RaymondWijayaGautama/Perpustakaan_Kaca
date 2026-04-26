<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrWawancara
 * 
 * @property int $ID_WAWANCARA
 * @property int|null $ID_PENDAFTARAN
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_WAWANCARA
 * @property string|null $WAKTU_WAWANCARA
 * @property string|null $TEMPAT_WAWANCARA
 * @property string|null $HASIL_WAWANCARA
 * 
 * @property TrPendaftaran|null $tr_pendaftaran
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class TrWawancara extends Model
{
	protected $table = 'tr_wawancara';
	protected $primaryKey = 'ID_WAWANCARA';
	public $timestamps = false;

	protected $casts = [
		'ID_PENDAFTARAN' => 'int',
		'TGL_WAWANCARA' => 'datetime'
	];

	protected $fillable = [
		'ID_PENDAFTARAN',
		'NIP_KARYAWAN',
		'TGL_WAWANCARA',
		'WAKTU_WAWANCARA',
		'TEMPAT_WAWANCARA',
		'HASIL_WAWANCARA'
	];

	public function tr_pendaftaran()
	{
		return $this->belongsTo(TrPendaftaran::class, 'ID_PENDAFTARAN');
	}

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
