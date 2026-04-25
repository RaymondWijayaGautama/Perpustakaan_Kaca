<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModulAjar
 * 
 * @property int $ID_MODUL_AJAR
 * @property int|null $ID_KKTP
 * @property string|null $JUDUL_MODUL
 * @property string|null $KEGIATAN_AWAL
 * @property string|null $KEGIATAN_INTI
 * @property string|null $KEGIATAN_PENUTUP
 * @property string|null $LAMPIRAN_MODUL
 * 
 * @property MstKriteriaKetuntasan|null $mst_kriteria_ketuntasan
 * @property Collection|MstKriteriaKetuntasan[] $mst_kriteria_ketuntasans
 *
 * @package App\Models
 */
class ModulAjar extends Model
{
	protected $table = 'modul_ajar';
	protected $primaryKey = 'ID_MODUL_AJAR';
	public $timestamps = false;

	protected $casts = [
		'ID_KKTP' => 'int'
	];

	protected $fillable = [
		'ID_KKTP',
		'JUDUL_MODUL',
		'KEGIATAN_AWAL',
		'KEGIATAN_INTI',
		'KEGIATAN_PENUTUP',
		'LAMPIRAN_MODUL'
	];

	public function mst_kriteria_ketuntasan()
	{
		return $this->belongsTo(MstKriteriaKetuntasan::class, 'ID_KKTP');
	}

	public function mst_kriteria_ketuntasans()
	{
		return $this->hasMany(MstKriteriaKetuntasan::class, 'ID_MODUL_AJAR');
	}
}
