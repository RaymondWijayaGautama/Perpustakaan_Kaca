<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DokumenKaryawan
 * 
 * @property int $ID_DOK_KARYAWAN
 * @property string|null $NIP_KARYAWAN
 * @property string|null $NAMA_DOK_KARYAWAN
 * @property string|null $STATUS_DOK_KARYAWAN
 * @property string|null $LINK_DOK_KARYAWAN
 * 
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class DokumenKaryawan extends Model
{
	protected $table = 'dokumen_karyawan';
	protected $primaryKey = 'ID_DOK_KARYAWAN';
	public $timestamps = false;

	protected $fillable = [
		'NIP_KARYAWAN',
		'NAMA_DOK_KARYAWAN',
		'STATUS_DOK_KARYAWAN',
		'LINK_DOK_KARYAWAN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
