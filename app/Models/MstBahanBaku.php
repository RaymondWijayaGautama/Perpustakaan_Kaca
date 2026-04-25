<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstBahanBaku
 * 
 * @property int $ID_BAHAN_BAKU
 * @property string|null $NAMA_BAHAN
 * @property float|null $KUANTITI_STOK_BAHAN
 * @property float|null $HARGA_BELI
 * @property string|null $SATUAN_BAHAN_BAKU
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|DtPembelianBahanBaku[] $dt_pembelian_bahan_bakus
 * @property Collection|DtResep[] $dt_reseps
 *
 * @package App\Models
 */
class MstBahanBaku extends Model
{
	protected $table = 'mst_bahan_baku';
	protected $primaryKey = 'ID_BAHAN_BAKU';
	public $timestamps = false;

	protected $casts = [
		'KUANTITI_STOK_BAHAN' => 'float',
		'HARGA_BELI' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_BAHAN',
		'KUANTITI_STOK_BAHAN',
		'HARGA_BELI',
		'SATUAN_BAHAN_BAKU',
		'IS_DELETE'
	];

	public function dt_pembelian_bahan_bakus()
	{
		return $this->hasMany(DtPembelianBahanBaku::class, 'ID_BAHAN_BAKU');
	}

	public function dt_reseps()
	{
		return $this->hasMany(DtResep::class, 'ID_BAHAN_BAKU');
	}
}
