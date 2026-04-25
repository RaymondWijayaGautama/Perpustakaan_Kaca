<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJalurPendaftaran
 * 
 * @property int $ID_JALUR_PENDAFTARAN
 * @property string|null $NAMA_JALUR
 * @property Carbon|null $TGL_MULAI_JALUR
 * @property Carbon|null $TGL_SELESAI_JALUR
 * @property string|null $KETERANGAN_JALUR
 *
 * @package App\Models
 */
class RefJalurPendaftaran extends Model
{
	protected $table = 'ref_jalur_pendaftaran';
	protected $primaryKey = 'ID_JALUR_PENDAFTARAN';
	public $timestamps = false;

	protected $casts = [
		'TGL_MULAI_JALUR' => 'datetime',
		'TGL_SELESAI_JALUR' => 'datetime'
	];

	protected $fillable = [
		'NAMA_JALUR',
		'TGL_MULAI_JALUR',
		'TGL_SELESAI_JALUR',
		'KETERANGAN_JALUR'
	];
}
