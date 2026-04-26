<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LogBarangCoffeeshop
 * 
 * @property int $ID_LOG_BARANG_COFFEESHOP
 * @property int|null $ID_INVENTARIS_COFFEESHOP
 * @property Carbon|null $TGL_CEK_BARANG_COFFEESHOP
 * @property string|null $KONDISI_BARANG_COFFEESHOP
 * @property string|null $KET_BARANG_COFFEESHOP
 * 
 * @property InventarisBarangCoffeeshop|null $inventaris_barang_coffeeshop
 *
 * @package App\Models
 */
class LogBarangCoffeeshop extends Model
{
	protected $table = 'log_barang_coffeeshop';
	protected $primaryKey = 'ID_LOG_BARANG_COFFEESHOP';
	public $timestamps = false;

	protected $casts = [
		'ID_INVENTARIS_COFFEESHOP' => 'int',
		'TGL_CEK_BARANG_COFFEESHOP' => 'datetime'
	];

	protected $fillable = [
		'ID_INVENTARIS_COFFEESHOP',
		'TGL_CEK_BARANG_COFFEESHOP',
		'KONDISI_BARANG_COFFEESHOP',
		'KET_BARANG_COFFEESHOP'
	];

	public function inventaris_barang_coffeeshop()
	{
		return $this->belongsTo(InventarisBarangCoffeeshop::class, 'ID_INVENTARIS_COFFEESHOP');
	}
}
