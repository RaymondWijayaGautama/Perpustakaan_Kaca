<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PklSiswa
 * 
 * @property int $ID_PKL_SISWA
 * @property int|null $ID_PENDAF_PKL
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $ID_MITRA_PKL
 * @property string|null $NIP_KARYAWAN
 * @property string|null $STATUS_PKL
 * @property float|null $NILAI_PKL
 * @property string|null $JUDUL_LAPORAN_PKL
 * @property string|null $LINK_LAPORAN_PKL
 * @property string|null $LINK_GAMBAR_MAP
 * 
 * @property PendafPkl|null $pendaf_pkl
 * @property MstSiswa|null $mst_siswa
 * @property MitraPkl|null $mitra_pkl
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|MstKoleksiLaporan[] $mst_koleksi_laporans
 *
 * @package App\Models
 */
class PklSiswa extends Model
{
	protected $table = 'pkl_siswa';
	protected $primaryKey = 'ID_PKL_SISWA';
	public $timestamps = false;

	protected $casts = [
		'ID_PENDAF_PKL' => 'int',
		'ID_SISWA_TETAP' => 'int',
		'ID_MITRA_PKL' => 'int',
		'NILAI_PKL' => 'float'
	];

	protected $fillable = [
		'ID_PENDAF_PKL',
		'ID_SISWA_TETAP',
		'ID_MITRA_PKL',
		'NIP_KARYAWAN',
		'STATUS_PKL',
		'NILAI_PKL',
		'JUDUL_LAPORAN_PKL',
		'LINK_LAPORAN_PKL',
		'LINK_GAMBAR_MAP'
	];

	public function pendaf_pkl()
	{
		return $this->belongsTo(PendafPkl::class, 'ID_PENDAF_PKL');
	}

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function mitra_pkl()
	{
		return $this->belongsTo(MitraPkl::class, 'ID_MITRA_PKL');
	}

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function mst_koleksi_laporans()
	{
		return $this->hasMany(MstKoleksiLaporan::class, 'ID_PKL_SISWA');
	}
}
