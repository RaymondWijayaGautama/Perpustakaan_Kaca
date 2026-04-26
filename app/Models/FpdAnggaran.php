<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FpdAnggaran
 * 
 * @property int $ID_FPD
 * @property int|null $ID_PROGRAM_KERJA
 * @property Carbon|null $TGL_FPD
 * @property float|null $NOMINAL_ANGGARAN
 * @property float|null $NOMINAL_FPD
 * @property float|null $NOMINAL_SISA
 * @property string|null $NIP_VALIDATOR_FPD
 * 
 * @property MstProgramKerja|null $mst_program_kerja
 * @property Collection|DtlFpd[] $dtl_fpds
 *
 * @package App\Models
 */
class FpdAnggaran extends Model
{
	protected $table = 'fpd_anggaran';
	protected $primaryKey = 'ID_FPD';
	public $timestamps = false;

	protected $casts = [
		'ID_PROGRAM_KERJA' => 'int',
		'TGL_FPD' => 'datetime',
		'NOMINAL_ANGGARAN' => 'float',
		'NOMINAL_FPD' => 'float',
		'NOMINAL_SISA' => 'float'
	];

	protected $fillable = [
		'ID_PROGRAM_KERJA',
		'TGL_FPD',
		'NOMINAL_ANGGARAN',
		'NOMINAL_FPD',
		'NOMINAL_SISA',
		'NIP_VALIDATOR_FPD'
	];

	public function mst_program_kerja()
	{
		return $this->belongsTo(MstProgramKerja::class, 'ID_PROGRAM_KERJA');
	}

	public function dtl_fpds()
	{
		return $this->hasMany(DtlFpd::class, 'ID_FPD');
	}
}
