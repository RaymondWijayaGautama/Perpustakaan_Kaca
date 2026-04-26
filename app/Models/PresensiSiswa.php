<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PresensiSiswa
 * 
 * @property int $ID_PRESENSI_SISWA
 * @property int|null $ID_TR_JADWAL
 * @property int|null $ID_SISWA_KELAS
 * @property string|null $STATUS_PRESENSI_SISWA
 * 
 * @property TrJadwal|null $tr_jadwal
 * @property SiswaKela|null $siswa_kela
 *
 * @package App\Models
 */
class PresensiSiswa extends Model
{
	protected $table = 'presensi_siswa';
	protected $primaryKey = 'ID_PRESENSI_SISWA';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_JADWAL' => 'int',
		'ID_SISWA_KELAS' => 'int'
	];

	protected $fillable = [
		'ID_TR_JADWAL',
		'ID_SISWA_KELAS',
		'STATUS_PRESENSI_SISWA'
	];

	public function tr_jadwal()
	{
		return $this->belongsTo(TrJadwal::class, 'ID_TR_JADWAL');
	}

	public function siswa_kela()
	{
		return $this->belongsTo(SiswaKela::class, 'ID_SISWA_KELAS');
	}
}
