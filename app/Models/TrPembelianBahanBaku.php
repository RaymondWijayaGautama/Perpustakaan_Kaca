<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPembelianBahanBaku
 * 
 * @property int $ID_TR_PEMBELIAN_BAHAN
 * @property int|null $ID_JADWAL_COFFEESHOP
 * @property Carbon|null $TGL_PEMBELIAN_BAHAN
 * @property float|null $TOTAL_PEMBELIAN
 * @property string|null $LINK_NOTA_PEMBELIAN
 * 
 * @property JadwalCoffeeshop|null $jadwal_coffeeshop
 * @property Collection|DtPembelianBahanBaku[] $dt_pembelian_bahan_bakus
 *
 * @package App\Models
 */
class TrPembelianBahanBaku extends Model
{
	protected $table = 'tr_pembelian_bahan_baku';
	protected $primaryKey = 'ID_TR_PEMBELIAN_BAHAN';
	public $timestamps = false;

	protected $casts = [
		'ID_JADWAL_COFFEESHOP' => 'int',
		'TGL_PEMBELIAN_BAHAN' => 'datetime',
		'TOTAL_PEMBELIAN' => 'float'
	];

	protected $fillable = [
		'ID_JADWAL_COFFEESHOP',
		'TGL_PEMBELIAN_BAHAN',
		'TOTAL_PEMBELIAN',
		'LINK_NOTA_PEMBELIAN'
	];

	public function jadwal_coffeeshop()
	{
		return $this->belongsTo(JadwalCoffeeshop::class, 'ID_JADWAL_COFFEESHOP');
	}

	public function dt_pembelian_bahan_bakus()
	{
		return $this->hasMany(DtPembelianBahanBaku::class, 'ID_TR_PEMBELIAN_BAHAN');
	}
}
