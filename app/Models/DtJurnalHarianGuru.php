<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DtJurnalHarianGuru
 * 
 * @property int $ID_DT_JURNAL_GURU
 * @property int|null $ID_JURNAL_MENGAJAR
 * @property int|null $ID_LESSON_PLAN
 * @property Carbon|null $DT_TGL_JURNAL_GURU
 * @property Carbon|null $DT_WAKTU_MULAI_GURU
 * @property Carbon|null $DT_WAKTU_SELESAI_GURU
 * @property string|null $DT_KEGIATAN_GURU
 * @property string|null $DT_INDIKATOR_GURU
 * @property string|null $DT_TARGET_GURU
 * @property string|null $DT_KENDALA_GURU
 * @property string|null $DT_SOLUSI_GURU
 * @property string|null $DT_KET_GURU
 * 
 * @property TrJurnalMengajar|null $tr_jurnal_mengajar
 * @property TrLessonPlan|null $tr_lesson_plan
 *
 * @package App\Models
 */
class DtJurnalHarianGuru extends Model
{
	protected $table = 'dt_jurnal_harian_guru';
	protected $primaryKey = 'ID_DT_JURNAL_GURU';
	public $timestamps = false;

	protected $casts = [
		'ID_JURNAL_MENGAJAR' => 'int',
		'ID_LESSON_PLAN' => 'int',
		'DT_TGL_JURNAL_GURU' => 'datetime',
		'DT_WAKTU_MULAI_GURU' => 'datetime',
		'DT_WAKTU_SELESAI_GURU' => 'datetime'
	];

	protected $fillable = [
		'ID_JURNAL_MENGAJAR',
		'ID_LESSON_PLAN',
		'DT_TGL_JURNAL_GURU',
		'DT_WAKTU_MULAI_GURU',
		'DT_WAKTU_SELESAI_GURU',
		'DT_KEGIATAN_GURU',
		'DT_INDIKATOR_GURU',
		'DT_TARGET_GURU',
		'DT_KENDALA_GURU',
		'DT_SOLUSI_GURU',
		'DT_KET_GURU'
	];

	public function tr_jurnal_mengajar()
	{
		return $this->belongsTo(TrJurnalMengajar::class, 'ID_JURNAL_MENGAJAR');
	}

	public function tr_lesson_plan()
	{
		return $this->belongsTo(TrLessonPlan::class, 'ID_LESSON_PLAN');
	}
}
