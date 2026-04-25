<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DtlProgramKerja
 * 
 * @property int $ID_DT_PROGKER
 * @property int|null $ID_PROGRAM_KERJA
 * @property int|null $ID_REF_DANA
 * @property float|null $NOMINAL
 * @property Carbon|null $TGL_AWAL
 * @property Carbon|null $TGL_AKHIR
 * @property int|null $QTY
 * @property float|null $HARGA_SATUAN
 * @property int|null $VOLUME
 * @property string|null $SATUAN
 * @property float|null $TOTAL_PROGKER
 * 
 * @property MstProgramKerja|null $mst_program_kerja
 * @property RefSumberDana|null $ref_sumber_dana
 * @property Collection|DtlFpd[] $dtl_fpds
 *
 * @package App\Models
 */
class DtlProgramKerja extends Model
{
	protected $table = 'dtl_program_kerja';
	protected $primaryKey = 'ID_DT_PROGKER';
	public $timestamps = false;

	protected $casts = [
		'ID_PROGRAM_KERJA' => 'int',
		'ID_REF_DANA' => 'int',
		'NOMINAL' => 'float',
		'TGL_AWAL' => 'datetime',
		'TGL_AKHIR' => 'datetime',
		'QTY' => 'int',
		'HARGA_SATUAN' => 'float',
		'VOLUME' => 'int',
		'TOTAL_PROGKER' => 'float'
	];

	protected $fillable = [
		'ID_PROGRAM_KERJA',
		'ID_REF_DANA',
		'NOMINAL',
		'TGL_AWAL',
		'TGL_AKHIR',
		'QTY',
		'HARGA_SATUAN',
		'VOLUME',
		'SATUAN',
		'TOTAL_PROGKER'
	];

	public function mst_program_kerja()
	{
		return $this->belongsTo(MstProgramKerja::class, 'ID_PROGRAM_KERJA');
	}

	public function ref_sumber_dana()
	{
		return $this->belongsTo(RefSumberDana::class, 'ID_REF_DANA');
	}

	public function dtl_fpds()
	{
		return $this->hasMany(DtlFpd::class, 'ID_DT_PROGKER');
	}
}
