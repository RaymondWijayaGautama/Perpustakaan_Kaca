<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrEvaluasiMandiri
 * 
 * @property int $ID_TR_EVALUASI_MANDIRI
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_EVALUASI_MANDIRI
 * @property string|null $NIP_VALIDATOR_EVALUASI_MANDIRI
 * @property string|null $STATUS_EVAL_MANDIRI
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtEvaluasiMandiri[] $dt_evaluasi_mandiris
 *
 * @package App\Models
 */
class TrEvaluasiMandiri extends Model
{
	protected $table = 'tr_evaluasi_mandiri';
	protected $primaryKey = 'ID_TR_EVALUASI_MANDIRI';
	public $timestamps = false;

	protected $casts = [
		'TGL_EVALUASI_MANDIRI' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'TGL_EVALUASI_MANDIRI',
		'NIP_VALIDATOR_EVALUASI_MANDIRI',
		'STATUS_EVAL_MANDIRI'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_evaluasi_mandiris()
	{
		return $this->hasMany(DtEvaluasiMandiri::class, 'ID_TR_EVALUASI_MANDIRI');
	}
}
