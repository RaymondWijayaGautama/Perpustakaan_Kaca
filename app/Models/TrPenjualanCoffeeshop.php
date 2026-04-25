<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPenjualanCoffeeshop
 * 
 * @property int $ID_TR_PENJUALAN
 * @property int|null $ID_JADWAL_COFFEESHOP
 * @property int|null $ID_DISKON
 * @property Carbon|null $TGL_TR_PENJUALAN
 * @property string|null $NAMA_PEMBELI
 * @property Carbon|null $TGL_PEMESANAN
 * @property string|null $JENIS_PENJUALAN
 * @property float|null $DP_PENJUALAN
 * @property string|null $METODE_BAYAR
 * @property string|null $ALAMAT_PEMBELI
 * @property float|null $TOTAL_PENJUALAN
 * @property float|null $POTONGAN_DISKON
 * @property string|null $STATUS_TR_PENJUALAN
 * 
 * @property JadwalCoffeeshop|null $jadwal_coffeeshop
 * @property Diskon|null $diskon
 * @property Collection|DtPenjualan[] $dt_penjualans
 *
 * @package App\Models
 */
class TrPenjualanCoffeeshop extends Model
{
	protected $table = 'tr_penjualan_coffeeshop';
	protected $primaryKey = 'ID_TR_PENJUALAN';
	public $timestamps = false;

	protected $casts = [
		'ID_JADWAL_COFFEESHOP' => 'int',
		'ID_DISKON' => 'int',
		'TGL_TR_PENJUALAN' => 'datetime',
		'TGL_PEMESANAN' => 'datetime',
		'DP_PENJUALAN' => 'float',
		'TOTAL_PENJUALAN' => 'float',
		'POTONGAN_DISKON' => 'float'
	];

	protected $fillable = [
		'ID_JADWAL_COFFEESHOP',
		'ID_DISKON',
		'TGL_TR_PENJUALAN',
		'NAMA_PEMBELI',
		'TGL_PEMESANAN',
		'JENIS_PENJUALAN',
		'DP_PENJUALAN',
		'METODE_BAYAR',
		'ALAMAT_PEMBELI',
		'TOTAL_PENJUALAN',
		'POTONGAN_DISKON',
		'STATUS_TR_PENJUALAN'
	];

	public function jadwal_coffeeshop()
	{
		return $this->belongsTo(JadwalCoffeeshop::class, 'ID_JADWAL_COFFEESHOP');
	}

	public function diskon()
	{
		return $this->belongsTo(Diskon::class, 'ID_DISKON');
	}

	public function dt_penjualans()
	{
		return $this->hasMany(DtPenjualan::class, 'ID_TR_PENJUALAN');
	}
}
