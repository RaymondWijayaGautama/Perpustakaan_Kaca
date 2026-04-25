<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrStockOpname
 * 
 * @property int $ID_STOCK_OPNAME
 * @property int|null $ID_INVENTARIS
 * @property Carbon|null $TGL_STOCK_OPNAME
 * @property string|null $KONDISI_AKTUAL
 * @property string|null $KONDISI_DI_SISTEM
 * @property string|null $TINDAK_LANJUT
 * 
 * @property MstInventari|null $mst_inventari
 *
 * @package App\Models
 */
class TrStockOpname extends Model
{
	protected $table = 'tr_stock_opname';
	protected $primaryKey = 'ID_STOCK_OPNAME';
	public $timestamps = false;

	protected $casts = [
		'ID_INVENTARIS' => 'int',
		'TGL_STOCK_OPNAME' => 'datetime'
	];

	protected $fillable = [
		'ID_INVENTARIS',
		'TGL_STOCK_OPNAME',
		'KONDISI_AKTUAL',
		'KONDISI_DI_SISTEM',
		'TINDAK_LANJUT'
	];

	public function mst_inventari()
	{
		return $this->belongsTo(MstInventari::class, 'ID_INVENTARIS');
	}
}
