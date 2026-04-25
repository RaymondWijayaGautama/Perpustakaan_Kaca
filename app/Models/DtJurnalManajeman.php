<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DtJurnalManajeman
 * 
 * @property int $ID_DT_JURNAL_MANAJEMEN
 * @property int|null $ID_JURNAL_MANAJEMEN
 * @property Carbon|null $DT_TGL_JURNAL_MANAJEMEN
 * @property string|null $DT_PROGRAM_MANAJEMEN
 * @property string|null $DT_KEGIATAN_MANAJEMEN
 * @property string|null $DT_INDIKATOR_MANAJEMEN
 * @property string|null $DT_SASARAN_MANAJEMEN
 * @property string|null $DT_TARGET_MANAJEMEN
 * @property string|null $DT_KENDALA_MANAJEMEN
 * @property string|null $DT_SARAN_MANAJEMEN
 * @property string|null $DT_SOLUSI_MANAJEMEN
 * @property string|null $DT_KET_MANAJEMEN
 * 
 * @property TrJurnalManajeman|null $tr_jurnal_manajeman
 *
 * @package App\Models
 */
class DtJurnalManajeman extends Model
{
	protected $table = 'dt_jurnal_manajemen';
	protected $primaryKey = 'ID_DT_JURNAL_MANAJEMEN';
	public $timestamps = false;

	protected $casts = [
		'ID_JURNAL_MANAJEMEN' => 'int',
		'DT_TGL_JURNAL_MANAJEMEN' => 'datetime'
	];

	protected $fillable = [
		'ID_JURNAL_MANAJEMEN',
		'DT_TGL_JURNAL_MANAJEMEN',
		'DT_PROGRAM_MANAJEMEN',
		'DT_KEGIATAN_MANAJEMEN',
		'DT_INDIKATOR_MANAJEMEN',
		'DT_SASARAN_MANAJEMEN',
		'DT_TARGET_MANAJEMEN',
		'DT_KENDALA_MANAJEMEN',
		'DT_SARAN_MANAJEMEN',
		'DT_SOLUSI_MANAJEMEN',
		'DT_KET_MANAJEMEN'
	];

	public function tr_jurnal_manajeman()
	{
		return $this->belongsTo(TrJurnalManajeman::class, 'ID_JURNAL_MANAJEMEN');
	}
}
