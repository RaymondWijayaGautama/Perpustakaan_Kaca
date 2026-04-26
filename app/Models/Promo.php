<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Promo
 * 
 * @property int $ID_PROMO
 * @property string|null $NAMA_PROMO
 * @property float|null $HARGA_PROMO
 * @property string|null $STATUS_PROMO
 * @property Carbon|null $TGL_MULAI_PROMO
 * @property Carbon|null $TGL_SELESAI_PROMO
 * 
 * @property Collection|DtPenjualan[] $dt_penjualans
 * @property Collection|DtPromo[] $dt_promos
 *
 * @package App\Models
 */
class Promo extends Model
{
	protected $table = 'promo';
	protected $primaryKey = 'ID_PROMO';
	public $timestamps = false;

	protected $casts = [
		'HARGA_PROMO' => 'float',
		'TGL_MULAI_PROMO' => 'datetime',
		'TGL_SELESAI_PROMO' => 'datetime'
	];

	protected $fillable = [
		'NAMA_PROMO',
		'HARGA_PROMO',
		'STATUS_PROMO',
		'TGL_MULAI_PROMO',
		'TGL_SELESAI_PROMO'
	];

	public function dt_penjualans()
	{
		return $this->hasMany(DtPenjualan::class, 'ID_PROMO');
	}

	public function dt_promos()
	{
		return $this->hasMany(DtPromo::class, 'ID_PROMO');
	}
}
