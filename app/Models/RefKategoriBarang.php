<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKategoriBarang
 * 
 * @property int $ID_KAT_BARANG
 * @property string|null $NAMA_KAT_BARANG
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstInventari[] $mst_inventaris
 *
 * @package App\Models
 */
class RefKategoriBarang extends Model
{
	protected $table = 'ref_kategori_barang';
	protected $primaryKey = 'ID_KAT_BARANG';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_KAT_BARANG',
		'IS_DELETE'
	];

	public function mst_inventaris()
	{
		return $this->hasMany(MstInventari::class, 'ID_KAT_BARANG');
	}
}
