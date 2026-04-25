<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PendafPkl
 * 
 * @property int $ID_PENDAF_PKL
 * @property int|null $KODE_TA
 * @property Carbon|null $TGL_MULAI_PKL
 * @property Carbon|null $TGL_SELESAI_PKL
 * @property string|null $STATUS_PKL
 * 
 * @property RefTum|null $ref_tum
 * @property Collection|PklSiswa[] $pkl_siswas
 *
 * @package App\Models
 */
class PendafPkl extends Model
{
	protected $table = 'pendaf_pkl';
	protected $primaryKey = 'ID_PENDAF_PKL';
	public $timestamps = false;

	protected $casts = [
		'KODE_TA' => 'int',
		'TGL_MULAI_PKL' => 'datetime',
		'TGL_SELESAI_PKL' => 'datetime'
	];

	protected $fillable = [
		'KODE_TA',
		'TGL_MULAI_PKL',
		'TGL_SELESAI_PKL',
		'STATUS_PKL'
	];

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function pkl_siswas()
	{
		return $this->hasMany(PklSiswa::class, 'ID_PENDAF_PKL');
	}
}
