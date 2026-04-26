<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtNilai
 * 
 * @property int $ID_DT_NILAI
 * @property int|null $ID_SISWA_KELAS
 * @property string|null $KODE_MAPEL
 * @property string|null $JENIS_NILAI
 * @property float|null $NILAI_KOMPONEN
 * 
 * @property SiswaKela|null $siswa_kela
 * @property MstMapel|null $mst_mapel
 *
 * @package App\Models
 */
class DtNilai extends Model
{
	protected $table = 'dt_nilai';
	protected $primaryKey = 'ID_DT_NILAI';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_KELAS' => 'int',
		'NILAI_KOMPONEN' => 'float'
	];

	protected $fillable = [
		'ID_SISWA_KELAS',
		'KODE_MAPEL',
		'JENIS_NILAI',
		'NILAI_KOMPONEN'
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
