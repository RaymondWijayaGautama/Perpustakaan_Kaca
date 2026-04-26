<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstRuang
 * 
 * @property int $ID_RUANG
 * @property string|null $NAMA_RUANG
 * @property int|null $LUAS
 * @property int|null $KAPASITAS
 * @property string|null $KONDISI_RUANGAN
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|DtPeminjamanInventari[] $dt_peminjaman_inventaris
 * @property Collection|TrPenempatanInventari[] $tr_penempatan_inventaris
 *
 * @package App\Models
 */
class MstRuang extends Model
{
	protected $table = 'mst_ruang';
	protected $primaryKey = 'ID_RUANG';
	public $timestamps = false;

	protected $casts = [
		'LUAS' => 'int',
		'KAPASITAS' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_RUANG',
		'LUAS',
		'KAPASITAS',
		'KONDISI_RUANGAN',
		'IS_DELETE'
	];

	public function dt_peminjaman_inventaris()
	{
		return $this->hasMany(DtPeminjamanInventari::class, 'ID_RUANG');
	}

	public function tr_penempatan_inventaris()
	{
		return $this->hasMany(TrPenempatanInventari::class, 'ID_RUANG');
	}
}
