<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrRapor
 * 
 * @property int $ID_TR_RAPOR
 * @property int|null $ID_SISWA_KELAS
 * @property string|null $KODE_MAPEL
 * @property float|null $NILAI_AKHIR_MAPEL
 * 
 * @property SiswaKela|null $siswa_kela
 * @property MstMapel|null $mst_mapel
 *
 * @package App\Models
 */
class TrRapor extends Model
{
	protected $table = 'tr_rapor';
	protected $primaryKey = 'ID_TR_RAPOR';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_KELAS' => 'int',
		'NILAI_AKHIR_MAPEL' => 'float'
	];

	protected $fillable = [
		'ID_SISWA_KELAS',
		'KODE_MAPEL',
		'NILAI_AKHIR_MAPEL'
	];

	public function siswa_kela()
	{
		return $this->belongsTo(SiswaKela::class, 'ID_SISWA_KELAS');
	}

	public function mst_mapel()
	{
		return $this->belongsTo(MstMapel::class, 'KODE_MAPEL');
	}
}
