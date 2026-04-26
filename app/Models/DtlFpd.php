<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtlFpd
 * 
 * @property int $ID_DT_FPD
 * @property int|null $ID_FPD
 * @property int|null $ID_DT_PROGKER
 * @property int|null $QTY
 * @property float|null $HARGA_SATUAN
 * @property int|null $VOLUME
 * @property string|null $SATUAN
 * @property float|null $TOTAL
 * @property string|null $LINK_BUKTI_NOTA_FPD
 * 
 * @property FpdAnggaran|null $fpd_anggaran
 * @property DtlProgramKerja|null $dtl_program_kerja
 *
 * @package App\Models
 */
class DtlFpd extends Model
{
	protected $table = 'dtl_fpd';
	protected $primaryKey = 'ID_DT_FPD';
	public $timestamps = false;

	protected $casts = [
		'ID_FPD' => 'int',
		'ID_DT_PROGKER' => 'int',
		'QTY' => 'int',
		'HARGA_SATUAN' => 'float',
		'VOLUME' => 'int',
		'TOTAL' => 'float'
	];

	protected $fillable = [
		'ID_FPD',
		'ID_DT_PROGKER',
		'QTY',
		'HARGA_SATUAN',
		'VOLUME',
		'SATUAN',
		'TOTAL',
		'LINK_BUKTI_NOTA_FPD'
	];

	public function fpd_anggaran()
	{
		return $this->belongsTo(FpdAnggaran::class, 'ID_FPD');
	}

	public function dtl_program_kerja()
	{
		return $this->belongsTo(DtlProgramKerja::class, 'ID_DT_PROGKER');
	}
}
