<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JadwalCoffeeshop
 * 
 * @property int $ID_JADWAL_COFFEESHOP
 * @property int|null $ID_ROLE_COFFEESHOP
 * @property int|null $ID_SISWA_TETAP
 * @property string|null $NIP_KARYAWAN
 * @property int $ID_NILAI_KINERJA
 * @property Carbon|null $TANGGAL_JADWAL_COFFEESHOP
 * @property string|null $STATUS_PRESENSI
 * @property string|null $NIP_VALIDATOR_PRESENSI
 * 
 * @property RoleCoffeeshop|null $role_coffeeshop
 * @property MstSiswa|null $mst_siswa
 * @property MstKaryawan|null $mst_karyawan
 * @property NilaiKinerjaCoffeeshop $nilai_kinerja_coffeeshop
 * @property Collection|NilaiKinerjaCoffeeshop[] $nilai_kinerja_coffeeshops
 * @property Collection|TrPembelianBahanBaku[] $tr_pembelian_bahan_bakus
 * @property Collection|TrPenjualanCoffeeshop[] $tr_penjualan_coffeeshops
 *
 * @package App\Models
 */
class JadwalCoffeeshop extends Model
{
	protected $table = 'jadwal_coffeeshop';
	protected $primaryKey = 'ID_JADWAL_COFFEESHOP';
	public $timestamps = false;

	protected $casts = [
		'ID_ROLE_COFFEESHOP' => 'int',
		'ID_SISWA_TETAP' => 'int',
		'ID_NILAI_KINERJA' => 'int',
		'TANGGAL_JADWAL_COFFEESHOP' => 'datetime'
	];

	protected $fillable = [
		'ID_ROLE_COFFEESHOP',
		'ID_SISWA_TETAP',
		'NIP_KARYAWAN',
		'ID_NILAI_KINERJA',
		'TANGGAL_JADWAL_COFFEESHOP',
		'STATUS_PRESENSI',
		'NIP_VALIDATOR_PRESENSI'
	];

	public function role_coffeeshop()
	{
		return $this->belongsTo(RoleCoffeeshop::class, 'ID_ROLE_COFFEESHOP');
	}

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function nilai_kinerja_coffeeshop()
	{
		return $this->belongsTo(NilaiKinerjaCoffeeshop::class, 'ID_NILAI_KINERJA');
	}

	public function nilai_kinerja_coffeeshops()
	{
		return $this->hasMany(NilaiKinerjaCoffeeshop::class, 'ID_JADWAL_COFFEESHOP');
	}

	public function tr_pembelian_bahan_bakus()
	{
		return $this->hasMany(TrPembelianBahanBaku::class, 'ID_JADWAL_COFFEESHOP');
	}

	public function tr_penjualan_coffeeshops()
	{
		return $this->hasMany(TrPenjualanCoffeeshop::class, 'ID_JADWAL_COFFEESHOP');
	}
}
