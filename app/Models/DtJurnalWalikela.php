<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DtJurnalWalikela
 * 
 * @property int $ID_DT_JURNAL_WALI
 * @property int|null $ID_JURNAL_WALI
 * @property Carbon|null $DT_TGL_JURNAL_WALI
 * @property string|null $DT_PROGRAM_WALI
 * @property string|null $DT_KEGIATAN_WALI
 * @property string|null $DT_INDIKATOR_WALI
 * @property string|null $DT_SASARAN_WALI
 * @property string|null $DT_TARGET_WALI
 * @property string|null $DT_KENDALA_WALI
 * @property string|null $DT_SARAN_WALI
 * @property string|null $DT_SOLUSI_WALI
 * @property string|null $DT_KET_WALI
 * 
 * @property TrJurnalWalikela|null $tr_jurnal_walikela
 *
 * @package App\Models
 */
class DtJurnalWalikela extends Model
{
	protected $table = 'dt_jurnal_walikelas';
	protected $primaryKey = 'ID_DT_JURNAL_WALI';
	public $timestamps = false;

	protected $casts = [
		'ID_JURNAL_WALI' => 'int',
		'DT_TGL_JURNAL_WALI' => 'datetime'
	];

	protected $fillable = [
		'ID_JURNAL_WALI',
		'DT_TGL_JURNAL_WALI',
		'DT_PROGRAM_WALI',
		'DT_KEGIATAN_WALI',
		'DT_INDIKATOR_WALI',
		'DT_SASARAN_WALI',
		'DT_TARGET_WALI',
		'DT_KENDALA_WALI',
		'DT_SARAN_WALI',
		'DT_SOLUSI_WALI',
		'DT_KET_WALI'
	];

	public function tr_jurnal_walikela()
	{
		return $this->belongsTo(TrJurnalWalikela::class, 'ID_JURNAL_WALI');
	}
}
