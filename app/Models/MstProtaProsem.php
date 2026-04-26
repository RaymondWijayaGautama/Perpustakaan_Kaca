<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MstProtaProsem
 * 
 * @property int $ID_PROTA_PROSEM
 * @property int|null $ID_ATP
 * @property string|null $KODE_MAPEL
 * @property string|null $POKOK_MATERI
 * @property float|null $ALOKASI_JP
 * @property string|null $ALOKASI_TA
 * @property string|null $ALOKASI_SEMESTER
 * @property string|null $ALOKASI_BULAN
 * @property int|null $ALOKASI_MINGGU
 * @property string|null $NIP_VALIDATOR_PROTA_PROSEM
 * @property bool|null $IS_DELETE
 * 
 * @property MstArahTujuanPembelajaran|null $mst_arah_tujuan_pembelajaran
 * @property MstMapel|null $mst_mapel
 *
 * @package App\Models
 */
class MstProtaProsem extends Model
{
	protected $table = 'mst_prota_prosem';
	protected $primaryKey = 'ID_PROTA_PROSEM';
	public $timestamps = false;

	protected $casts = [
		'ID_ATP' => 'int',
		'ALOKASI_JP' => 'float',
		'ALOKASI_MINGGU' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_ATP',
		'KODE_MAPEL',
		'POKOK_MATERI',
		'ALOKASI_JP',
		'ALOKASI_TA',
		'ALOKASI_SEMESTER',
		'ALOKASI_BULAN',
		'ALOKASI_MINGGU',
		'NIP_VALIDATOR_PROTA_PROSEM',
		'IS_DELETE'
	];

	public function mst_arah_tujuan_pembelajaran()
	{
		return $this->belongsTo(MstArahTujuanPembelajaran::class, 'ID_ATP');
	}

	public function mst_mapel()
	{
		return $this->belongsTo(MstMapel::class, 'KODE_MAPEL');
	}
}
