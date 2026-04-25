<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrJurnalManajeman
 * 
 * @property int $ID_JURNAL_MANAJEMEN
 * @property string|null $NIP_KARYAWAN
 * @property string|null $KETUGASAN_MANAJEMEN
 * @property string|null $MINGGU_MANAJEMEN
 * @property string|null $TGL_PENYERAHAN_MANAJEMEN
 * @property string|null $NIP_VALIDATOR_MANAJEMEN
 * @property string|null $STATUS_JURNAL_MANAJEMEN
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtJurnalManajeman[] $dt_jurnal_manajemen
 *
 * @package App\Models
 */
class TrJurnalManajeman extends Model
{
	protected $table = 'tr_jurnal_manajemen';
	protected $primaryKey = 'ID_JURNAL_MANAJEMEN';
	public $timestamps = false;

	protected $fillable = [
		'NIP_KARYAWAN',
		'KETUGASAN_MANAJEMEN',
		'MINGGU_MANAJEMEN',
		'TGL_PENYERAHAN_MANAJEMEN',
		'NIP_VALIDATOR_MANAJEMEN',
		'STATUS_JURNAL_MANAJEMEN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_jurnal_manajemen()
	{
		return $this->hasMany(DtJurnalManajeman::class, 'ID_JURNAL_MANAJEMEN');
	}
}
