<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstPertanyaanTracer
 * 
 * @property int $ID_MST_PERTANYAAN
 * @property int|null $ID_TRACER
 * @property string|null $PERTANYAAN_TRACER
 * @property bool|null $IS_DELETE
 * 
 * @property TrTracer|null $tr_tracer
 * @property Collection|TrTracer[] $tr_tracers
 *
 * @package App\Models
 */
class MstPertanyaanTracer extends Model
{
	protected $table = 'mst_pertanyaan_tracer';
	protected $primaryKey = 'ID_MST_PERTANYAAN';
	public $timestamps = false;

	protected $casts = [
		'ID_TRACER' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_TRACER',
		'PERTANYAAN_TRACER',
		'IS_DELETE'
	];

	public function tr_tracer()
	{
		return $this->belongsTo(TrTracer::class, 'ID_TRACER');
	}

	public function tr_tracers()
	{
		return $this->hasMany(TrTracer::class, 'ID_MST_PERTANYAAN');
	}
}
