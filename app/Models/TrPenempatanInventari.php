<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPenempatanInventari
 * 
 * @property int $ID_PENEMPATAN
 * @property int|null $ID_INVENTARIS
 * @property int|null $ID_RUANG
 * @property Carbon|null $TGL_MULAI_PENEMPATAN
 * @property Carbon|null $TGL_SELESAI_PENEMPATAN
 * 
 * @property MstInventari|null $mst_inventari
 * @property MstRuang|null $mst_ruang
 *
 * @package App\Models
 */
class TrPenempatanInventari extends Model
{
	protected $table = 'tr_penempatan_inventaris';
	protected $primaryKey = 'ID_PENEMPATAN';
	public $timestamps = false;

	protected $casts = [
		'ID_INVENTARIS' => 'int',
		'ID_RUANG' => 'int',
		'TGL_MULAI_PENEMPATAN' => 'datetime',
		'TGL_SELESAI_PENEMPATAN' => 'datetime'
	];

	protected $fillable = [
		'ID_INVENTARIS',
		'ID_RUANG',
		'TGL_MULAI_PENEMPATAN',
		'TGL_SELESAI_PENEMPATAN'
	];

	public function mst_inventari()
	{
		return $this->belongsTo(MstInventari::class, 'ID_INVENTARIS');
	}

	public function mst_ruang()
	{
		return $this->belongsTo(MstRuang::class, 'ID_RUANG');
	}
}
