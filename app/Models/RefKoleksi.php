<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKoleksi
 * 
 * @property int $ID_REF_KOLEKSI
 * @property string|null $NO_KATEGORI_BUKU
 * @property string|null $DESKRIPSI_KATEGORI
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstKoleksiBuku[] $mst_koleksi_bukus
 *
 * @package App\Models
 */
class RefKoleksi extends Model
{
	protected $table = 'ref_koleksi';
	protected $primaryKey = 'ID_REF_KOLEKSI';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NO_KATEGORI_BUKU',
		'DESKRIPSI_KATEGORI',
		'IS_DELETE'
	];

	public function mst_koleksi_bukus()
	{
		return $this->hasMany(MstKoleksiBuku::class, 'ID_REF_KOLEKSI');
	}
}
