<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NilaiKinerjaCoffeeshop
 * 
 * @property int $ID_NILAI_KINERJA
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $ID_JADWAL_COFFEESHOP
 * @property string|null $KET_KINERJA
 * @property float|null $NILAI_KINERJA
 * @property string|null $NIP_VALIDATOR_KOORDINATOR
 * 
 * @property MstSiswa|null $mst_siswa
 * @property JadwalCoffeeshop|null $jadwal_coffeeshop
 * @property Collection|JadwalCoffeeshop[] $jadwal_coffeeshops
 *
 * @package App\Models
 */
class NilaiKinerjaCoffeeshop extends Model
{
	protected $table = 'nilai_kinerja_coffeeshop';
	protected $primaryKey = 'ID_NILAI_KINERJA';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'ID_JADWAL_COFFEESHOP' => 'int',
		'NILAI_KINERJA' => 'float'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'ID_JADWAL_COFFEESHOP',
		'KET_KINERJA',
		'NILAI_KINERJA',
		'NIP_VALIDATOR_KOORDINATOR'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function jadwal_coffeeshop()
	{
		return $this->belongsTo(JadwalCoffeeshop::class, 'ID_JADWAL_COFFEESHOP');
	}

	public function jadwal_coffeeshops()
	{
		return $this->hasMany(JadwalCoffeeshop::class, 'ID_NILAI_KINERJA');
	}
}
