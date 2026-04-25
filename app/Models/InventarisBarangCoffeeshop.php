<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InventarisBarangCoffeeshop
 * 
 * @property int $ID_INVENTARIS_COFFEESHOP
 * @property string|null $NAMA_BARANG_COFFEESHOP
 * @property Carbon|null $TGL_BELI_BARANG_COFFEESHOP
 * @property float|null $HARGA_BARANG_COFFEESHOP
 * @property int|null $JML_BARANG_COFFEESHOP
 * @property string|null $STATUS_BARANG_COFFEESHOP
 * 
 * @property Collection|LogBarangCoffeeshop[] $log_barang_coffeeshops
 *
 * @package App\Models
 */
class InventarisBarangCoffeeshop extends Model
{
	protected $table = 'inventaris_barang_coffeeshop';
	protected $primaryKey = 'ID_INVENTARIS_COFFEESHOP';
	public $timestamps = false;

	protected $casts = [
		'TGL_BELI_BARANG_COFFEESHOP' => 'datetime',
		'HARGA_BARANG_COFFEESHOP' => 'float',
		'JML_BARANG_COFFEESHOP' => 'int'
	];

	protected $fillable = [
		'NAMA_BARANG_COFFEESHOP',
		'TGL_BELI_BARANG_COFFEESHOP',
		'HARGA_BARANG_COFFEESHOP',
		'JML_BARANG_COFFEESHOP',
		'STATUS_BARANG_COFFEESHOP'
	];

	public function log_barang_coffeeshops()
	{
		return $this->hasMany(LogBarangCoffeeshop::class, 'ID_INVENTARIS_COFFEESHOP');
	}
}
