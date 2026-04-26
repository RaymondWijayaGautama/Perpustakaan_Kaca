<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPenghapusanInventari
 * 
 * @property int $ID_PENGHAPUSAN_INV
 * @property int|null $ID_INVENTARIS
 * @property Carbon|null $TGL_PENGHAPUSAN_INV
 * @property string|null $KET_PENGHAPUSAN_INV
 * @property float|null $NOMINAL_PENJUALAN
 * @property Carbon|null $TGL_PENJUALAN_INV
 * @property string|null $NIP_VALIDATOR_PENGHAPUSAN
 * 
 * @property MstInventari|null $mst_inventari
 *
 * @package App\Models
 */
class TrPenghapusanInventari extends Model
{
	protected $table = 'tr_penghapusan_inventaris';
	protected $primaryKey = 'ID_PENGHAPUSAN_INV';
	public $timestamps = false;

	protected $casts = [
		'ID_INVENTARIS' => 'int',
		'TGL_PENGHAPUSAN_INV' => 'datetime',
		'NOMINAL_PENJUALAN' => 'float',
		'TGL_PENJUALAN_INV' => 'datetime'
	];

	protected $fillable = [
		'ID_INVENTARIS',
		'TGL_PENGHAPUSAN_INV',
		'KET_PENGHAPUSAN_INV',
		'NOMINAL_PENJUALAN',
		'TGL_PENJUALAN_INV',
		'NIP_VALIDATOR_PENGHAPUSAN'
	];

	public function mst_inventari()
	{
		return $this->belongsTo(MstInventari::class, 'ID_INVENTARIS');
	}
}
