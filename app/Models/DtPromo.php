<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtPromo
 * 
 * @property int $ID_DT_PROMO
 * @property int|null $ID_PROMO
 * @property int|null $ID_MENU_COFFEESHOP
 * @property float|null $SUBTOTAL_PROMO
 * 
 * @property Promo|null $promo
 * @property MenuCoffeeshop|null $menu_coffeeshop
 *
 * @package App\Models
 */
class DtPromo extends Model
{
	protected $table = 'dt_promo';
	protected $primaryKey = 'ID_DT_PROMO';
	public $timestamps = false;

	protected $casts = [
		'ID_PROMO' => 'int',
		'ID_MENU_COFFEESHOP' => 'int',
		'SUBTOTAL_PROMO' => 'float'
	];

	protected $fillable = [
		'ID_PROMO',
		'ID_MENU_COFFEESHOP',
		'SUBTOTAL_PROMO'
	];

	public function promo()
	{
		return $this->belongsTo(Promo::class, 'ID_PROMO');
	}

	public function menu_coffeeshop()
	{
		return $this->belongsTo(MenuCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}
}
