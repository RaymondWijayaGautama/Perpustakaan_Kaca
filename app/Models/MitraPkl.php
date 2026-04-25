<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MitraPkl
 * 
 * @property int $ID_MITRA_PKL
 * @property string|null $NAMA_MITRA_PKL
 * @property string|null $STATUS_MITRA_PKL
 * @property string|null $ALAMAT_MITRA_PKL
 * @property string|null $NO_TELP_MITRA_PKL
 * @property string|null $JARAK_TEMPAT_PKL
 * @property string|null $NO_MOU_PKL
 * 
 * @property Collection|PklSiswa[] $pkl_siswas
 *
 * @package App\Models
 */
class MitraPkl extends Model
{
	protected $table = 'mitra_pkl';
	protected $primaryKey = 'ID_MITRA_PKL';
	public $timestamps = false;

	protected $fillable = [
		'NAMA_MITRA_PKL',
		'STATUS_MITRA_PKL',
		'ALAMAT_MITRA_PKL',
		'NO_TELP_MITRA_PKL',
		'JARAK_TEMPAT_PKL',
		'NO_MOU_PKL'
	];

	public function pkl_siswas()
	{
		return $this->hasMany(PklSiswa::class, 'ID_MITRA_PKL');
	}
}
