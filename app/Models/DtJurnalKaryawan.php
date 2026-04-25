<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DtJurnalKaryawan
 * 
 * @property int $ID_DT_JURNAL_KARYAWAN
 * @property int|null $ID_TR_JURNAL_KARYAWAN
 * @property Carbon|null $TGL_DT_KARYAWAN
 * @property Carbon|null $WAKTU_MULAI_KARYAWAN
 * @property Carbon|null $WAKTU_SELESAI_KARYAWAN
 * @property string|null $KEGIATAN_KARYAWAN
 * @property string|null $INDIKATOR_KARYAWAN
 * @property string|null $STATUS_KEGIATAN_KARYAWAN
 * @property string|null $SARAN_KEGIATAN_KARYAWAN
 * @property string|null $SOLUSI_KEGIATAN_KARYAWAN
 * @property string|null $KETERANGAN_KEGIATAN_KARYAWAN
 * 
 * @property TrJurnalKaryawan|null $tr_jurnal_karyawan
 *
 * @package App\Models
 */
class DtJurnalKaryawan extends Model
{
	protected $table = 'dt_jurnal_karyawan';
	protected $primaryKey = 'ID_DT_JURNAL_KARYAWAN';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_JURNAL_KARYAWAN' => 'int',
		'TGL_DT_KARYAWAN' => 'datetime',
		'WAKTU_MULAI_KARYAWAN' => 'datetime',
		'WAKTU_SELESAI_KARYAWAN' => 'datetime'
	];

	protected $fillable = [
		'ID_TR_JURNAL_KARYAWAN',
		'TGL_DT_KARYAWAN',
		'WAKTU_MULAI_KARYAWAN',
		'WAKTU_SELESAI_KARYAWAN',
		'KEGIATAN_KARYAWAN',
		'INDIKATOR_KARYAWAN',
		'STATUS_KEGIATAN_KARYAWAN',
		'SARAN_KEGIATAN_KARYAWAN',
		'SOLUSI_KEGIATAN_KARYAWAN',
		'KETERANGAN_KEGIATAN_KARYAWAN'
	];

	public function tr_jurnal_karyawan()
	{
		return $this->belongsTo(TrJurnalKaryawan::class, 'ID_TR_JURNAL_KARYAWAN');
	}
}
