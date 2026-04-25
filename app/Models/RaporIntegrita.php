<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RaporIntegrita
 * 
 * @property int $ID_R_INTEGRITAS
 * @property int|null $ID_REF_INTEGRITAS
 * @property int|null $ID_SISWA_TETAP
 * @property float|null $NILAI_INTEGRITAS
 * 
 * @property RefIntegrita|null $ref_integrita
 * @property MstSiswa|null $mst_siswa
 *
 * @package App\Models
 */
class RaporIntegrita extends Model
{
	protected $table = 'rapor_integritas';
	protected $primaryKey = 'ID_R_INTEGRITAS';
	public $timestamps = false;

	protected $casts = [
		'ID_REF_INTEGRITAS' => 'int',
		'ID_SISWA_TETAP' => 'int',
		'NILAI_INTEGRITAS' => 'float'
	];

	protected $fillable = [
		'ID_REF_INTEGRITAS',
		'ID_SISWA_TETAP',
		'NILAI_INTEGRITAS'
	];

	public function ref_integrita()
	{
		return $this->belongsTo(RefIntegrita::class, 'ID_REF_INTEGRITAS');
	}

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}
}
