<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DokumenPrestasiPelatihan
 * 
 * @property int $ID_DOK_PRESTASI_PELATIHAN
 * @property int|null $ID_PRESTASI_PELATIHAN
 * @property string|null $NAMA_DOK_PRESTASI_PELATIHAN
 * @property string|null $LINK_DOK_PRESTASI_PELATIHAN
 * 
 * @property TrPrestasiPelatihanKaryawan|null $tr_prestasi_pelatihan_karyawan
 *
 * @package App\Models
 */
class DokumenPrestasiPelatihan extends Model
{
	protected $table = 'dokumen_prestasi_pelatihan';
	protected $primaryKey = 'ID_DOK_PRESTASI_PELATIHAN';
	public $timestamps = false;

	protected $casts = [
		'ID_PRESTASI_PELATIHAN' => 'int'
	];

	protected $fillable = [
		'ID_PRESTASI_PELATIHAN',
		'NAMA_DOK_PRESTASI_PELATIHAN',
		'LINK_DOK_PRESTASI_PELATIHAN'
	];

	public function tr_prestasi_pelatihan_karyawan()
	{
		return $this->belongsTo(TrPrestasiPelatihanKaryawan::class, 'ID_PRESTASI_PELATIHAN');
	}
}
