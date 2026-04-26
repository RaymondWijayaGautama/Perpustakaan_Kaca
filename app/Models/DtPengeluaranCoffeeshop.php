<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtPengeluaranCoffeeshop
 * 
 * @property int $ID_DT_PENGELUARAN
 * @property int|null $ID_TR_PENGELUARAN
 * @property string|null $LINK_NOTA_PENGELUARAN
 * @property float|null $SUBTOTAL_PENGELUARAN
 * @property string|null $DESKRIPSI_PENGELUARAN
 * 
 * @property TrPengeluaranCoffeeshop|null $tr_pengeluaran_coffeeshop
 *
 * @package App\Models
 */
class DtPengeluaranCoffeeshop extends Model
{
	protected $table = 'dt_pengeluaran_coffeeshop';
	protected $primaryKey = 'ID_DT_PENGELUARAN';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_PENGELUARAN' => 'int',
		'SUBTOTAL_PENGELUARAN' => 'float'
	];

	protected $fillable = [
		'ID_TR_PENGELUARAN',
		'LINK_NOTA_PENGELUARAN',
		'SUBTOTAL_PENGELUARAN',
		'DESKRIPSI_PENGELUARAN'
	];

	public function tr_pengeluaran_coffeeshop()
	{
		return $this->belongsTo(TrPengeluaranCoffeeshop::class, 'ID_TR_PENGELUARAN');
	}
}
