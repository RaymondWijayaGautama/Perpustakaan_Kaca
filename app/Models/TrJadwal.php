<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJadwal
 * 
 * @property int $ID_TR_JADWAL
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KODE_MAPEL
 * @property int|null $ID_KELAS
 * @property string|null $HARI_JADWAL
 * @property Carbon|null $TGL_JADWAL
 * @property string|null $RUANGAN_MAPEL
 * @property Carbon|null $JAM_MULAI_MAPEL
 * @property Carbon|null $JAM_SELESAI_MAPEL
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property MstMapel|null $mst_mapel
 * @property MstKela|null $mst_kela
 * @property Collection|PresensiSiswa[] $presensi_siswas
 *
 * @package App\Models
 */
class TrJadwal extends Model
{
	protected $table = 'tr_jadwal';
	protected $primaryKey = 'ID_TR_JADWAL';
	public $timestamps = false;

	protected $casts = [
		'ID_KELAS' => 'int',
		'TGL_JADWAL' => 'datetime',
		'JAM_MULAI_MAPEL' => 'datetime',
		'JAM_SELESAI_MAPEL' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'KODE_MAPEL',
		'ID_KELAS',
		'HARI_JADWAL',
		'TGL_JADWAL',
		'RUANGAN_MAPEL',
		'JAM_MULAI_MAPEL',
		'JAM_SELESAI_MAPEL'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function mst_mapel()
	{
		return $this->belongsTo(MstMapel::class, 'KODE_MAPEL');
	}

	public function mst_kela()
	{
		return $this->belongsTo(MstKela::class, 'ID_KELAS');
	}

	public function presensi_siswas()
	{
		return $this->hasMany(PresensiSiswa::class, 'ID_TR_JADWAL');
	}
}
