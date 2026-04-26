<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtResep
 * 
 * @property int $ID_DT_RESEP
 * @property int|null $ID_BAHAN_BAKU
 * @property int|null $ID_MENU_COFFEESHOP
 * @property float|null $KUANTITI_BAHAN_RESEP
 * @property string|null $SATUAN_BAHAN_RESEP
 * @property bool|null $IS_DELETE
 * 
 * @property MstBahanBaku|null $mst_bahan_baku
 * @property MenuCoffeeshop|null $menu_coffeeshop
 *
 * @package App\Models
 */
class DtResep extends Model
{
	protected $table = 'dt_resep';
	protected $primaryKey = 'ID_DT_RESEP';
	public $timestamps = false;

	protected $casts = [
		'ID_BAHAN_BAKU' => 'int',
		'ID_MENU_COFFEESHOP' => 'int',
		'KUANTITI_BAHAN_RESEP' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_BAHAN_BAKU',
		'ID_MENU_COFFEESHOP',
		'KUANTITI_BAHAN_RESEP',
		'SATUAN_BAHAN_RESEP',
		'IS_DELETE'
	];

	public function mst_bahan_baku()
	{
		return $this->belongsTo(MstBahanBaku::class, 'ID_BAHAN_BAKU');
	}

	public function menu_coffeeshop()
	{
		return $this->belongsTo(MenuCoffeeshop::class, 'ID_MENU_COFFEESHOP');
	}
}
