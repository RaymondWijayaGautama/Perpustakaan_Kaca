<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstArahTujuanPembelajaran
 * 
 * @property int $ID_ATP
 * @property int|null $ID_TUJUAN_PEMB
 * @property int|null $ID_GURU_MAPEL
 * @property string|null $DESKRIPSI_ATP
 * @property bool|null $IS_DELETE
 * 
 * @property MstTujuanPembelajaran|null $mst_tujuan_pembelajaran
 * @property GuruMapel|null $guru_mapel
 * @property Collection|MstKriteriaKetuntasan[] $mst_kriteria_ketuntasans
 * @property Collection|MstProtaProsem[] $mst_prota_prosems
 * @property Collection|TrLessonPlan[] $tr_lesson_plans
 *
 * @package App\Models
 */
class MstArahTujuanPembelajaran extends Model
{
	protected $table = 'mst_arah_tujuan_pembelajaran';
	protected $primaryKey = 'ID_ATP';
	public $timestamps = false;

	protected $casts = [
		'ID_TUJUAN_PEMB' => 'int',
		'ID_GURU_MAPEL' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_TUJUAN_PEMB',
		'ID_GURU_MAPEL',
		'DESKRIPSI_ATP',
		'IS_DELETE'
	];

	public function mst_tujuan_pembelajaran()
	{
		return $this->belongsTo(MstTujuanPembelajaran::class, 'ID_TUJUAN_PEMB');
	}

	public function guru_mapel()
	{
		return $this->belongsTo(GuruMapel::class, 'ID_GURU_MAPEL');
	}

	public function mst_kriteria_ketuntasans()
	{
		return $this->hasMany(MstKriteriaKetuntasan::class, 'ID_ATP');
	}

	public function mst_prota_prosems()
	{
		return $this->hasMany(MstProtaProsem::class, 'ID_ATP');
	}

	public function tr_lesson_plans()
	{
		return $this->hasMany(TrLessonPlan::class, 'ID_ATP');
	}
}
