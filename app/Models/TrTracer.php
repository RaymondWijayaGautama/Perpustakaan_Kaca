<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrTracer
 * 
 * @property int $ID_TRACER
 * @property int|null $ID_MST_PERTANYAAN
 * @property int|null $ID_SISWA_TETAP
 * @property string|null $JAWABAN_PERTANYAAN
 * 
 * @property MstPertanyaanTracer|null $mst_pertanyaan_tracer
 * @property MstSiswa|null $mst_siswa
 * @property Collection|MstPertanyaanTracer[] $mst_pertanyaan_tracers
 *
 * @package App\Models
 */
class TrTracer extends Model
{
	protected $table = 'tr_tracer';
	protected $primaryKey = 'ID_TRACER';
	public $timestamps = false;

	protected $casts = [
		'ID_MST_PERTANYAAN' => 'int',
		'ID_SISWA_TETAP' => 'int'
	];

	protected $fillable = [
		'ID_MST_PERTANYAAN',
		'ID_SISWA_TETAP',
		'JAWABAN_PERTANYAAN'
	];

	public function mst_pertanyaan_tracer()
	{
		return $this->belongsTo(MstPertanyaanTracer::class, 'ID_MST_PERTANYAAN');
	}

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function mst_pertanyaan_tracers()
	{
		return $this->hasMany(MstPertanyaanTracer::class, 'ID_TRACER');
	}
}
