<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPembelianInventari
 * 
 * @property int $ID_PEMBELIAN
 * @property int|null $ID_REF_PEMBELIAN
 * @property string|null $LINK_NOTA_BELI_INV
 * @property Carbon|null $TGL_PESAN_INV
 * @property Carbon|null $TGL_DATANG_INV
 * @property string|null $STATUS_PEMBELIAN
 * 
 * @property RefPembelian|null $ref_pembelian
 * @property Collection|MstInventari[] $mst_inventaris
 *
 * @package App\Models
 */
class TrPembelianInventari extends Model
{
	protected $table = 'tr_pembelian_inventaris';
	protected $primaryKey = 'ID_PEMBELIAN';
	public $timestamps = false;

	protected $casts = [
		'ID_REF_PEMBELIAN' => 'int',
		'TGL_PESAN_INV' => 'datetime',
		'TGL_DATANG_INV' => 'datetime'
	];

	protected $fillable = [
		'ID_REF_PEMBELIAN',
		'LINK_NOTA_BELI_INV',
		'TGL_PESAN_INV',
		'TGL_DATANG_INV',
		'STATUS_PEMBELIAN'
	];

	public function ref_pembelian()
	{
		return $this->belongsTo(RefPembelian::class, 'ID_REF_PEMBELIAN');
	}

	public function mst_inventaris()
	{
		return $this->hasMany(MstInventari::class, 'ID_PEMBELIAN');
	}
}
