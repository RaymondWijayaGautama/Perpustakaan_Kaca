<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuCoffeeshop
 * 
 * @property int $ID_MENU_COFFEESHOP
 * @property string|null $NAMA_MENU_COFFEESHOP
 * @property float|null $HARGA_JUAL_MENU
 * @property float|null $HARGA_POKOK_MENU
 * @property string|null $KATEGORI_MENU
 * @property string|null $LINK_FOTO_MENU
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|DtPenjualan[] $dt_penjualans
 * @property Collection|DtPromo[] $dt_promos
 * @property Collection|DtResep[] $dt_reseps
 * @property Collection|KeranjangCoffeeshop[] $keranjang_coffeeshops
 * @property Collection|StokMenu[] $stok_menus
 *
 * @package App\Models
 */
class MenuCoffeeshop extends Model
{
	protected $table = 'menu_coffeeshop';
	protected $primaryKey = 'ID_MENU_COFFEESHOP';
	public $timestamps = false;

	protected $casts = [
		'HARGA_JUAL_MENU' => 'float',
		'HARGA_POKOK_MENU' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_MENU_COFFEESHOP',
		'HARGA_JUAL_MENU',
		'HARGA_POKOK_MENU',
		'KATEGORI_MENU',
		'LINK_FOTO_MENU',
		'IS_DELETE'
	];

	public function dt_penjualans()
	{
		return $this->hasMany(DtPenjualan::class, 'ID_MENU_COFFEESHOP');
	}

	public function dt_promos()
	{
		return $this->hasMany(DtPromo::class, 'ID_MENU_COFFEESHOP');
	}

	public function dt_reseps()
	{
		return $this->hasMany(DtResep::class, 'ID_MENU_COFFEESHOP');
	}

	public function keranjang_coffeeshops()
	{
		return $this->hasMany(KeranjangCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}

	public function stok_menus()
	{
		return $this->hasMany(StokMenu::class, 'ID_MENU_COFFEESHOP');
	}
}
