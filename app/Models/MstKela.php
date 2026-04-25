<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKela
 * 
 * @property int $ID_KELAS
 * @property int|null $ID_TINGKAT
 * @property string|null $KODE_KELAS
 * @property bool|null $IS_DELETE
 * @property int|null $KUOTA_KELAS
 * 
 * @property MstTingkat|null $mst_tingkat
 * @property Collection|SiswaKela[] $siswa_kelas
 * @property Collection|TrJadwal[] $tr_jadwals
 * @property Collection|TrLessonPlan[] $tr_lesson_plans
 *
 * @package App\Models
 */
class MstKela extends Model
{
	protected $table = 'mst_kelas';
	protected $primaryKey = 'ID_KELAS';
	public $timestamps = false;

	protected $casts = [
		'ID_TINGKAT' => 'int',
		'IS_DELETE' => 'bool',
		'KUOTA_KELAS' => 'int'
	];

	protected $fillable = [
		'ID_TINGKAT',
		'KODE_KELAS',
		'IS_DELETE',
		'KUOTA_KELAS'
	];

	public function mst_tingkat()
	{
		return $this->belongsTo(MstTingkat::class, 'ID_TINGKAT');
	}

	public function siswa_kelas()
	{
		return $this->hasMany(SiswaKela::class, 'ID_KELAS');
	}

	public function tr_jadwals()
	{
		return $this->hasMany(TrJadwal::class, 'ID_KELAS');
	}

	public function tr_lesson_plans()
	{
		return $this->hasMany(TrLessonPlan::class, 'ID_KELAS');
	}
}
