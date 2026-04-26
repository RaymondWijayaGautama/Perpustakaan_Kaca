<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstCapaianPembelajaran
 * 
 * @property int $ID_CAP_PEMB
 * @property string|null $DESKRIPSI_CAP_PEMB
 * @property string|null $ELEMEN_CAP_PEMB
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstTujuanPembelajaran[] $mst_tujuan_pembelajarans
 *
 * @package App\Models
 */
class MstCapaianPembelajaran extends Model
{
	protected $table = 'mst_capaian_pembelajaran';
	protected $primaryKey = 'ID_CAP_PEMB';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'DESKRIPSI_CAP_PEMB',
		'ELEMEN_CAP_PEMB',
		'IS_DELETE'
	];

	public function mst_tujuan_pembelajarans()
	{
		return $this->hasMany(MstTujuanPembelajaran::class, 'ID_CAP_PEMB');
	}
}
