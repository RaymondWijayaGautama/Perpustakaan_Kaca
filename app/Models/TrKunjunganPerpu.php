<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrKunjunganPerpu
 * 
 * @property int $ID_KUNJUNGAN
 * @property int|null $ID_SISWA_TETAP
 * @property Carbon|null $START_KUNJUNGAN
 * @property Carbon|null $END_KUNJUNGAN
 * 
 * @property MstSiswa|null $mst_siswa
 *
 * @package App\Models
 */
class TrKunjunganPerpu extends Model
{
	protected $table = 'tr_kunjungan_perpus';
	protected $primaryKey = 'ID_KUNJUNGAN';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'START_KUNJUNGAN' => 'datetime',
		'END_KUNJUNGAN' => 'datetime'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'START_KUNJUNGAN',
		'END_KUNJUNGAN'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}
}
