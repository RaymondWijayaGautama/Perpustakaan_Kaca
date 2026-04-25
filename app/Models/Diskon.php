<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Diskon
 * 
 * @property int $ID_DISKON
 * @property string|null $NAMA_DISKON
 * @property float|null $PERSEN_DISKON
 * @property string|null $STATUS_DISKON
 * @property Carbon|null $TGL_MULAI_DISKON
 * @property Carbon|null $TGL_SELESAI_DISKON
 * 
 * @property Collection|TrPenjualanCoffeeshop[] $tr_penjualan_coffeeshops
 *
 * @package App\Models
 */
class Diskon extends Model
{
	protected $table = 'diskon';
	protected $primaryKey = 'ID_DISKON';
	public $timestamps = false;

	protected $casts = [
		'PERSEN_DISKON' => 'float',
		'TGL_MULAI_DISKON' => 'datetime',
		'TGL_SELESAI_DISKON' => 'datetime'
	];

	protected $fillable = [
		'NAMA_DISKON',
		'PERSEN_DISKON',
		'STATUS_DISKON',
		'TGL_MULAI_DISKON',
		'TGL_SELESAI_DISKON'
	];

	public function tr_penjualan_coffeeshops()
	{
		return $this->hasMany(TrPenjualanCoffeeshop::class, 'ID_DISKON');
	}
}
