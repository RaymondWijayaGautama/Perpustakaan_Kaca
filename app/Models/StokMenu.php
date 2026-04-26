<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StokMenu
 * 
 * @property int $ID_STOK_MENU
 * @property int|null $ID_MENU_COFFEESHOP
 * @property Carbon|null $TGL_STOK_MENU
 * @property int|null $JML_STOK_MENU
 * 
 * @property MenuCoffeeshop|null $menu_coffeeshop
 *
 * @package App\Models
 */
class StokMenu extends Model
{
	protected $table = 'stok_menu';
	protected $primaryKey = 'ID_STOK_MENU';
	public $timestamps = false;

	protected $casts = [
		'ID_MENU_COFFEESHOP' => 'int',
		'TGL_STOK_MENU' => 'datetime',
		'JML_STOK_MENU' => 'int'
	];

	protected $fillable = [
		'ID_MENU_COFFEESHOP',
		'TGL_STOK_MENU',
		'JML_STOK_MENU'
	];

	public function menu_coffeeshop()
	{
		return $this->belongsTo(MenuCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}
}
