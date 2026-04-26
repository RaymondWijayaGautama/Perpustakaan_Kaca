<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPengeluaranCoffeeshop
 * 
 * @property int $ID_TR_PENGELUARAN
 * @property Carbon|null $TGL_TR_PENGELUARAN
 * @property float|null $TOTAL_PENGELUARAN
 * @property string|null $KET_PENGELUARAN
 * 
 * @property Collection|DtPengeluaranCoffeeshop[] $dt_pengeluaran_coffeeshops
 *
 * @package App\Models
 */
class TrPengeluaranCoffeeshop extends Model
{
	protected $table = 'tr_pengeluaran_coffeeshop';
	protected $primaryKey = 'ID_TR_PENGELUARAN';
	public $timestamps = false;

	protected $casts = [
		'TGL_TR_PENGELUARAN' => 'datetime',
		'TOTAL_PENGELUARAN' => 'float'
	];

	protected $fillable = [
		'TGL_TR_PENGELUARAN',
		'TOTAL_PENGELUARAN',
		'KET_PENGELUARAN'
	];

	public function dt_pengeluaran_coffeeshops()
	{
		return $this->hasMany(DtPengeluaranCoffeeshop::class, 'ID_TR_PENGELUARAN');
	}
}
