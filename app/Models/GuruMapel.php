<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GuruMapel
 * 
 * @property int $ID_GURU_MAPEL
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KODE_MAPEL
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property MstMapel|null $mst_mapel
 * @property Collection|MstArahTujuanPembelajaran[] $mst_arah_tujuan_pembelajarans
 *
 * @package App\Models
 */
class GuruMapel extends Model
{
	protected $table = 'guru_mapel';
	protected $primaryKey = 'ID_GURU_MAPEL';
	public $timestamps = false;

	protected $fillable = [
		'NIP_KARYAWAN',
		'KODE_MAPEL'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function mst_mapel()
	{
		return $this->belongsTo(MstMapel::class, 'KODE_MAPEL');
	}

	public function mst_arah_tujuan_pembelajarans()
	{
		return $this->hasMany(MstArahTujuanPembelajaran::class, 'ID_GURU_MAPEL');
	}
}
