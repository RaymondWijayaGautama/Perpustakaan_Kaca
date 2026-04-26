<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HstKela
 * 
 * @property int $ID_HST_KELAS
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $KODE_TA
 * @property string|null $KELAS
 * 
 * @property MstSiswa|null $mst_siswa
 * @property RefTum|null $ref_tum
 *
 * @package App\Models
 */
class HstKela extends Model
{
	protected $table = 'hst_kelas';
	protected $primaryKey = 'ID_HST_KELAS';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'KODE_TA' => 'int'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'KODE_TA',
		'KELAS'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}
}
