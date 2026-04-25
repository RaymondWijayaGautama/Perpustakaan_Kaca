<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstMapel
 * 
 * @property string $KODE_MAPEL
 * @property int|null $ID_KURIKULUM
 * @property int|null $ID_PROG_KEAHLIAN
 * @property int|null $ID_KONSENTRASI_KEAHLIAN
 * @property string|null $KATEGORI_KEJURUAN
 * @property string|null $NAMA_MAPEL
 * @property float|null $JP
 * @property float|null $TOTAL_JP
 * @property string|null $FASE
 * @property string|null $SEMESTER_MAPEL
 * @property bool|null $IS_DELETE
 * 
 * @property MstKurikulum|null $mst_kurikulum
 * @property RefProgramKeahlian|null $ref_program_keahlian
 * @property RefKonsentrasiKeahlian|null $ref_konsentrasi_keahlian
 * @property Collection|DtNilai[] $dt_nilais
 * @property Collection|GuruMapel[] $guru_mapels
 * @property Collection|MstProtaProsem[] $mst_prota_prosems
 * @property Collection|TrJadwal[] $tr_jadwals
 * @property Collection|TrLessonPlan[] $tr_lesson_plans
 * @property Collection|TrRapor[] $tr_rapors
 *
 * @package App\Models
 */
class MstMapel extends Model
{
	protected $table = 'mst_mapel';
	protected $primaryKey = 'KODE_MAPEL';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID_KURIKULUM' => 'int',
		'ID_PROG_KEAHLIAN' => 'int',
		'ID_KONSENTRASI_KEAHLIAN' => 'int',
		'JP' => 'float',
		'TOTAL_JP' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_KURIKULUM',
		'ID_PROG_KEAHLIAN',
		'ID_KONSENTRASI_KEAHLIAN',
		'KATEGORI_KEJURUAN',
		'NAMA_MAPEL',
		'JP',
		'TOTAL_JP',
		'FASE',
		'SEMESTER_MAPEL',
		'IS_DELETE'
	];

	public function mst_kurikulum()
	{
		return $this->belongsTo(MstKurikulum::class, 'ID_KURIKULUM');
	}

	public function ref_program_keahlian()
	{
		return $this->belongsTo(RefProgramKeahlian::class, 'ID_PROG_KEAHLIAN');
	}

	public function ref_konsentrasi_keahlian()
	{
		return $this->belongsTo(RefKonsentrasiKeahlian::class, 'ID_KONSENTRASI_KEAHLIAN');
	}

	public function dt_nilais()
	{
		return $this->hasMany(DtNilai::class, 'KODE_MAPEL');
	}

	public function guru_mapels()
	{
		return $this->hasMany(GuruMapel::class, 'KODE_MAPEL');
	}

	public function mst_prota_prosems()
	{
		return $this->hasMany(MstProtaProsem::class, 'KODE_MAPEL');
	}

	public function tr_jadwals()
	{
		return $this->hasMany(TrJadwal::class, 'KODE_MAPEL');
	}

	public function tr_lesson_plans()
	{
		return $this->hasMany(TrLessonPlan::class, 'KODE_MAPEL');
	}

	public function tr_rapors()
	{
		return $this->hasMany(TrRapor::class, 'KODE_MAPEL');
	}
}
