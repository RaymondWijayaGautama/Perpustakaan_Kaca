<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiswaKela
 * 
 * @property int $ID_SISWA_KELAS
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $ID_KELAS
 * @property int|null $KODE_TA
 * 
 * @property MstSiswa|null $mst_siswa
 * @property MstKela|null $mst_kela
 * @property RefTum|null $ref_tum
 * @property Collection|DtNilai[] $dt_nilais
 * @property Collection|PresensiSiswa[] $presensi_siswas
 * @property Collection|TrRapor[] $tr_rapors
 *
 * @package App\Models
 */
class SiswaKela extends Model
{
	protected $table = 'siswa_kelas';
	protected $primaryKey = 'ID_SISWA_KELAS';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'ID_KELAS' => 'int',
		'KODE_TA' => 'int'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'ID_KELAS',
		'KODE_TA'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function mst_kela()
	{
		return $this->belongsTo(MstKela::class, 'ID_KELAS');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function dt_nilais()
	{
		return $this->hasMany(DtNilai::class, 'ID_SISWA_KELAS');
	}

	public function presensi_siswas()
	{
		return $this->hasMany(PresensiSiswa::class, 'ID_SISWA_KELAS');
	}

	public function tr_rapors()
	{
		return $this->hasMany(TrRapor::class, 'ID_SISWA_KELAS');
	}
}
