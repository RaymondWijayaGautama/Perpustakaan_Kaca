<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstTujuanPembelajaran
 * 
 * @property int $ID_TUJUAN_PEMB
 * @property int|null $ID_CAP_PEMB
 * @property string|null $DESKRIPSI_TUJUAN_PEMB
 * @property bool|null $IS_DELETE
 * 
 * @property MstCapaianPembelajaran|null $mst_capaian_pembelajaran
 * @property Collection|MstArahTujuanPembelajaran[] $mst_arah_tujuan_pembelajarans
 *
 * @package App\Models
 */
class MstTujuanPembelajaran extends Model
{
	protected $table = 'mst_tujuan_pembelajaran';
	protected $primaryKey = 'ID_TUJUAN_PEMB';
	public $timestamps = false;

	protected $casts = [
		'ID_CAP_PEMB' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_CAP_PEMB',
		'DESKRIPSI_TUJUAN_PEMB',
		'IS_DELETE'
	];

	public function mst_capaian_pembelajaran()
	{
		return $this->belongsTo(MstCapaianPembelajaran::class, 'ID_CAP_PEMB');
	}

	public function mst_arah_tujuan_pembelajarans()
	{
		return $this->hasMany(MstArahTujuanPembelajaran::class, 'ID_TUJUAN_PEMB');
	}
}
