<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKriteriaKetuntasan
 * 
 * @property int $ID_KKTP
 * @property int|null $ID_ATP
 * @property int $ID_MODUL_AJAR
 * @property string|null $INTERVAL_NILAI
 * @property string|null $DESKRIPSI_INTERVAL
 * @property bool|null $IS_DELETE
 * 
 * @property MstArahTujuanPembelajaran|null $mst_arah_tujuan_pembelajaran
 * @property ModulAjar $modul_ajar
 * @property Collection|ModulAjar[] $modul_ajars
 *
 * @package App\Models
 */
class MstKriteriaKetuntasan extends Model
{
	protected $table = 'mst_kriteria_ketuntasan';
	protected $primaryKey = 'ID_KKTP';
	public $timestamps = false;

	protected $casts = [
		'ID_ATP' => 'int',
		'ID_MODUL_AJAR' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_ATP',
		'ID_MODUL_AJAR',
		'INTERVAL_NILAI',
		'DESKRIPSI_INTERVAL',
		'IS_DELETE'
	];

	public function mst_arah_tujuan_pembelajaran()
	{
		return $this->belongsTo(MstArahTujuanPembelajaran::class, 'ID_ATP');
	}

	public function modul_ajar()
	{
		return $this->belongsTo(ModulAjar::class, 'ID_MODUL_AJAR');
	}

	public function modul_ajars()
	{
		return $this->hasMany(ModulAjar::class, 'ID_KKTP');
	}
}
