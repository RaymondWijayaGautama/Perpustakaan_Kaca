<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtPembelianBahanBaku
 * 
 * @property int $ID_DT_PEMBELIAN_BAHAN
 * @property int|null $ID_BAHAN_BAKU
 * @property int|null $ID_TR_PEMBELIAN_BAHAN
 * @property float|null $JUMLAH_PEMBELIAN
 * @property string|null $SATUAN_PEMBELIAN_BAHAN_BAKU
 * 
 * @property MstBahanBaku|null $mst_bahan_baku
 * @property TrPembelianBahanBaku|null $tr_pembelian_bahan_baku
 *
 * @package App\Models
 */
class DtPembelianBahanBaku extends Model
{
	protected $table = 'dt_pembelian_bahan_baku';
	protected $primaryKey = 'ID_DT_PEMBELIAN_BAHAN';
	public $timestamps = false;

	protected $casts = [
		'ID_BAHAN_BAKU' => 'int',
		'ID_TR_PEMBELIAN_BAHAN' => 'int',
		'JUMLAH_PEMBELIAN' => 'float'
	];

	protected $fillable = [
		'ID_BAHAN_BAKU',
		'ID_TR_PEMBELIAN_BAHAN',
		'JUMLAH_PEMBELIAN',
		'SATUAN_PEMBELIAN_BAHAN_BAKU'
	];

	public function mst_bahan_baku()
	{
		return $this->belongsTo(MstBahanBaku::class, 'ID_BAHAN_BAKU');
	}

	public function tr_pembelian_bahan_baku()
	{
		return $this->belongsTo(TrPembelianBahanBaku::class, 'ID_TR_PEMBELIAN_BAHAN');
	}
}
