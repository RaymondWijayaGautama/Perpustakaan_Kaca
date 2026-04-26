<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJurnalMengajar
 * 
 * @property int $ID_JURNAL_MENGAJAR
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KETUGASAN_GURU
 * @property string|null $MINGGU_KE
 * @property Carbon|null $TGL_PENYERAHAN
 * @property string|null $NIP_VALIDATOR_PENGAJARAN
 * @property string|null $STATUS_JURNAL_MENGAJAR
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtJurnalHarianGuru[] $dt_jurnal_harian_gurus
 *
 * @package App\Models
 */
class TrJurnalMengajar extends Model
{
	protected $table = 'tr_jurnal_mengajar';
	protected $primaryKey = 'ID_JURNAL_MENGAJAR';
	public $timestamps = false;

	protected $casts = [
		'TGL_PENYERAHAN' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'KETUGASAN_GURU',
		'MINGGU_KE',
		'TGL_PENYERAHAN',
		'NIP_VALIDATOR_PENGAJARAN',
		'STATUS_JURNAL_MENGAJAR'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_jurnal_harian_gurus()
	{
		return $this->hasMany(DtJurnalHarianGuru::class, 'ID_JURNAL_MENGAJAR');
	}
}
