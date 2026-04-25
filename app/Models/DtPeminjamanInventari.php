<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtPeminjamanInventari
 * 
 * @property int $ID_DT_PINJAM_INV
 * @property int|null $ID_TR_PEMINJAMAN_INV
 * @property int|null $ID_INVENTARIS
 * @property int|null $ID_RUANG
 * @property string|null $KONDISI_SEBELUM
 * @property string|null $KONDISI_SESUDAH
 * 
 * @property TrPeminjamanInventari|null $tr_peminjaman_inventari
 * @property MstInventari|null $mst_inventari
 * @property MstRuang|null $mst_ruang
 *
 * @package App\Models
 */
class DtPeminjamanInventari extends Model
{
	protected $table = 'dt_peminjaman_inventaris';
	protected $primaryKey = 'ID_DT_PINJAM_INV';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_PEMINJAMAN_INV' => 'int',
		'ID_INVENTARIS' => 'int',
		'ID_RUANG' => 'int'
	];

	protected $fillable = [
		'ID_TR_PEMINJAMAN_INV',
		'ID_INVENTARIS',
		'ID_RUANG',
		'KONDISI_SEBELUM',
		'KONDISI_SESUDAH'
	];

	public function tr_peminjaman_inventari()
	{
		return $this->belongsTo(TrPeminjamanInventari::class, 'ID_TR_PEMINJAMAN_INV');
	}

	public function mst_inventari()
	{
		return $this->belongsTo(MstInventari::class, 'ID_INVENTARIS');
	}

	public function mst_ruang()
	{
		return $this->belongsTo(MstRuang::class, 'ID_RUANG');
	}
}
