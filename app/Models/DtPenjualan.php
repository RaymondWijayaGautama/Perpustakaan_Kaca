<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtPenjualan
 * 
 * @property int $ID_DT_PENJUALAN
 * @property int|null $ID_TR_PENJUALAN
 * @property int|null $ID_MENU_COFFEESHOP
 * @property int|null $ID_PROMO
 * @property int|null $JML_ITEM_PENJUALAN
 * @property float|null $SUBTOTAL_PENJUALAN
 * 
 * @property TrPenjualanCoffeeshop|null $tr_penjualan_coffeeshop
 * @property MenuCoffeeshop|null $menu_coffeeshop
 * @property Promo|null $promo
 *
 * @package App\Models
 */
class DtPenjualan extends Model
{
	protected $table = 'dt_penjualan';
	protected $primaryKey = 'ID_DT_PENJUALAN';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_PENJUALAN' => 'int',
		'ID_MENU_COFFEESHOP' => 'int',
		'ID_PROMO' => 'int',
		'JML_ITEM_PENJUALAN' => 'int',
		'SUBTOTAL_PENJUALAN' => 'float'
	];

	protected $fillable = [
		'ID_TR_PENJUALAN',
		'ID_MENU_COFFEESHOP',
		'ID_PROMO',
		'JML_ITEM_PENJUALAN',
		'SUBTOTAL_PENJUALAN'
	];

	public function tr_penjualan_coffeeshop()
	{
		return $this->belongsTo(TrPenjualanCoffeeshop::class, 'ID_TR_PENJUALAN');
	}

	public function menu_coffeeshop()
	{
		return $this->belongsTo(MenuCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}

	public function promo()
	{
		return $this->belongsTo(Promo::class, 'ID_PROMO');
	}
}
