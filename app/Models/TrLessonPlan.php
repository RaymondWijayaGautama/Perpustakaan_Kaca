<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrLessonPlan
 * 
 * @property int $ID_LESSON_PLAN
 * @property int|null $ID_ATP
 * @property string|null $KODE_MAPEL
 * @property int|null $ID_KELAS
 * @property Carbon|null $TGL_MULAI_LESSON_PLAN
 * @property Carbon|null $TGL_SELESAI_LESSON_PLAN
 * @property string|null $MATERI_PELAJARAN
 * @property string|null $STATUS_LESSON_PLAN
 * @property string|null $NIP_VALIDATOR_LESSON_PLAN
 * 
 * @property MstArahTujuanPembelajaran|null $mst_arah_tujuan_pembelajaran
 * @property MstMapel|null $mst_mapel
 * @property MstKela|null $mst_kela
 * @property Collection|DtJurnalHarianGuru[] $dt_jurnal_harian_gurus
 *
 * @package App\Models
 */
class TrLessonPlan extends Model
{
	protected $table = 'tr_lesson_plan';
	protected $primaryKey = 'ID_LESSON_PLAN';
	public $timestamps = false;

	protected $casts = [
		'ID_ATP' => 'int',
		'ID_KELAS' => 'int',
		'TGL_MULAI_LESSON_PLAN' => 'datetime',
		'TGL_SELESAI_LESSON_PLAN' => 'datetime'
	];

	protected $fillable = [
		'ID_ATP',
		'KODE_MAPEL',
		'ID_KELAS',
		'TGL_MULAI_LESSON_PLAN',
		'TGL_SELESAI_LESSON_PLAN',
		'MATERI_PELAJARAN',
		'STATUS_LESSON_PLAN',
		'NIP_VALIDATOR_LESSON_PLAN'
	];

	public function mst_arah_tujuan_pembelajaran()
	{
		return $this->belongsTo(MstArahTujuanPembelajaran::class, 'ID_ATP');
	}

	public function mst_mapel()
	{
		return $this->belongsTo(MstMapel::class, 'KODE_MAPEL');
	}

	public function mst_kela()
	{
		return $this->belongsTo(MstKela::class, 'ID_KELAS');
	}

	public function dt_jurnal_harian_gurus()
	{
		return $this->hasMany(DtJurnalHarianGuru::class, 'ID_LESSON_PLAN');
	}
}
