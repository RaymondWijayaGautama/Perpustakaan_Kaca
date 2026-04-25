<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJurnalWalikela
 * 
 * @property int $ID_JURNAL_WALI
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KETUGASAN_WALI
 * @property string|null $MINGGU_WALI
 * @property string|null $TGL_PENYERAHAN_WALI
 * @property string|null $NIP_VALIDATOR_WALI
 * @property string|null $STATUS_JURNAL_WALI
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtJurnalWalikela[] $dt_jurnal_walikelas
 *
 * @package App\Models
 */
class TrJurnalWalikela extends Model
{
	protected $table = 'tr_jurnal_walikelas';
	protected $primaryKey = 'ID_JURNAL_WALI';
	public $timestamps = false;

	protected $fillable = [
		'NIP_KARYAWAN',
		'KETUGASAN_WALI',
		'MINGGU_WALI',
		'TGL_PENYERAHAN_WALI',
		'NIP_VALIDATOR_WALI',
		'STATUS_JURNAL_WALI'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_jurnal_walikelas()
	{
		return $this->hasMany(DtJurnalWalikela::class, 'ID_JURNAL_WALI');
	}
}
