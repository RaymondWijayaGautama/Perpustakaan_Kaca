<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PrestasiCalonSiswa
 * 
 * @property int $ID_PRESTASI_CALON
 * @property string|null $KODE_CALON
 * @property string|null $JENIS_PRESTASI
 * @property string|null $NAMA_PRESTASI
 * 
 * @property MstCalonSiswa|null $mst_calon_siswa
 *
 * @package App\Models
 */
class PrestasiCalonSiswa extends Model
{
	protected $table = 'prestasi_calon_siswa';
	protected $primaryKey = 'ID_PRESTASI_CALON';
	public $timestamps = false;

	protected $fillable = [
		'KODE_CALON',
		'JENIS_PRESTASI',
		'NAMA_PRESTASI'
	];

	public function mst_calon_siswa()
	{
		return $this->belongsTo(MstCalonSiswa::class, 'KODE_CALON');
	}
}
