<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstInventari
 * 
 * @property int $ID_INVENTARIS
 * @property int|null $ID_KAT_BARANG
 * @property int|null $ID_PEMBELIAN
 * @property int|null $ID_TR_LAPORAN
 * @property string|null $KODE_INVENTARIS
 * @property string|null $NAMA_INVENTARIS
 * @property float|null $NILAI_INVENTARIS
 * @property Carbon|null $TGL_HABIS_GARANSI
 * @property string|null $LINK_FOTO_BARANG
 * @property string|null $MEREK_INV
 * @property string|null $NO_SERI_INV
 * @property string|null $DIMENSI_INV
 * @property string|null $KETERANGAN_INV
 * @property Carbon|null $TGL_BELI_INV
 * @property string|null $KONDISI_TERAKHIR_INV
 * @property string|null $STATUS_INV
 * @property bool|null $IS_DELETE
 * 
 * @property RefKategoriBarang|null $ref_kategori_barang
 * @property TrPembelianInventari|null $tr_pembelian_inventari
 * @property TrLaporanKerusakan|null $tr_laporan_kerusakan
 * @property Collection|DtPeminjamanInventari[] $dt_peminjaman_inventaris
 * @property Collection|TrPenempatanInventari[] $tr_penempatan_inventaris
 * @property Collection|TrPenghapusanInventari[] $tr_penghapusan_inventaris
 * @property Collection|TrStockOpname[] $tr_stock_opnames
 *
 * @package App\Models
 */
class MstInventari extends Model
{
	protected $table = 'mst_inventaris';
	protected $primaryKey = 'ID_INVENTARIS';
	public $timestamps = false;

	protected $casts = [
		'ID_KAT_BARANG' => 'int',
		'ID_PEMBELIAN' => 'int',
		'ID_TR_LAPORAN' => 'int',
		'NILAI_INVENTARIS' => 'float',
		'TGL_HABIS_GARANSI' => 'datetime',
		'TGL_BELI_INV' => 'datetime',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_KAT_BARANG',
		'ID_PEMBELIAN',
		'ID_TR_LAPORAN',
		'KODE_INVENTARIS',
		'NAMA_INVENTARIS',
		'NILAI_INVENTARIS',
		'TGL_HABIS_GARANSI',
		'LINK_FOTO_BARANG',
		'MEREK_INV',
		'NO_SERI_INV',
		'DIMENSI_INV',
		'KETERANGAN_INV',
		'TGL_BELI_INV',
		'KONDISI_TERAKHIR_INV',
		'STATUS_INV',
		'IS_DELETE'
	];

	public function ref_kategori_barang()
	{
		return $this->belongsTo(RefKategoriBarang::class, 'ID_KAT_BARANG');
	}

	public function tr_pembelian_inventari()
	{
		return $this->belongsTo(TrPembelianInventari::class, 'ID_PEMBELIAN');
	}

	public function tr_laporan_kerusakan()
	{
		return $this->belongsTo(TrLaporanKerusakan::class, 'ID_TR_LAPORAN');
	}

	public function dt_peminjaman_inventaris()
	{
		return $this->hasMany(DtPeminjamanInventari::class, 'ID_INVENTARIS');
	}

	public function tr_penempatan_inventaris()
	{
		return $this->hasMany(TrPenempatanInventari::class, 'ID_INVENTARIS');
	}

	public function tr_penghapusan_inventaris()
	{
		return $this->hasMany(TrPenghapusanInventari::class, 'ID_INVENTARIS');
	}

	public function tr_stock_opnames()
	{
		return $this->hasMany(TrStockOpname::class, 'ID_INVENTARIS');
	}
}
