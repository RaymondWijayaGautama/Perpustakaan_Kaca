<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPkg
 * 
 * @property int $ID_TR_PKG
 * @property string|null $NIP_KARYAWAN
 * @property int|null $KODE_TA
 * @property Carbon|null $TGL_TR_PKG
 * @property string|null $NIP_EVALUATOR_PKG
 * @property string|null $CATATAN_EVALUATOR
 * @property string|null $NIP_VALIDATOR_PKG
 * @property float|null $NILAI_AKHIR_PKG
 * @property string|null $STATUS_TR_PKG
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property RefTum|null $ref_tum
 * @property Collection|DtTrPkg[] $dt_tr_pkgs
 *
 * @package App\Models
 */
class TrPkg extends Model
{
	protected $table = 'tr_pkg';
	protected $primaryKey = 'ID_TR_PKG';
	public $timestamps = false;

	protected $casts = [
		'KODE_TA' => 'int',
		'TGL_TR_PKG' => 'datetime',
		'NILAI_AKHIR_PKG' => 'float'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'KODE_TA',
		'TGL_TR_PKG',
		'NIP_EVALUATOR_PKG',
		'CATATAN_EVALUATOR',
		'NIP_VALIDATOR_PKG',
		'NILAI_AKHIR_PKG',
		'STATUS_TR_PKG'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function dt_tr_pkgs()
	{
		return $this->hasMany(DtTrPkg::class, 'ID_TR_PKG');
	}
}
