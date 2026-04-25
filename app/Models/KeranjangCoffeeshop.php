<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class KeranjangCoffeeshop
 * 
 * @property int $ID_KERANJANG
 * @property int|null $ID_MENU_COFFEESHOP
 * @property int|null $JML_ITEM_KERANJANG
 * @property float|null $SUBTOTAL_KERANJANG
 * 
 * @property MenuCoffeeshop|null $menu_coffeeshop
 *
 * @package App\Models
 */
class KeranjangCoffeeshop extends Model
{
	protected $table = 'keranjang_coffeeshop';
	protected $primaryKey = 'ID_KERANJANG';
	public $timestamps = false;

	protected $casts = [
		'ID_MENU_COFFEESHOP' => 'int',
		'JML_ITEM_KERANJANG' => 'int',
		'SUBTOTAL_KERANJANG' => 'float'
	];

	protected $fillable = [
		'ID_MENU_COFFEESHOP',
		'JML_ITEM_KERANJANG',
		'SUBTOTAL_KERANJANG'
	];

	public function menu_coffeeshop()
	{
		return $this->belongsTo(MenuCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}
}
