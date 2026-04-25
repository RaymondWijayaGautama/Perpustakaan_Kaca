<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefPembelian
 * 
 * @property int $ID_REF_PEMBELIAN
 * @property string|null $DESKRIPSI_PEMBELIAN
 * @property string|null $KODE_COA
 * 
 * @property Collection|TrPembelianInventari[] $tr_pembelian_inventaris
 *
 * @package App\Models
 */
class RefPembelian extends Model
{
	protected $table = 'ref_pembelian';
	protected $primaryKey = 'ID_REF_PEMBELIAN';
	public $timestamps = false;

	protected $fillable = [
		'DESKRIPSI_PEMBELIAN',
		'KODE_COA'
	];

	public function tr_pembelian_inventaris()
	{
		return $this->hasMany(TrPembelianInventari::class, 'ID_REF_PEMBELIAN');
	}
}
