<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKegiatan
 * 
 * @property int $ID_KEGIATAN
 * @property int|null $MST_ID_KEGIATAN
 * @property string|null $DESKRIPSI_KEGIATAN
 * @property bool|null $IS_DELETE
 * 
 * @property MstKegiatan|null $mst_kegiatan
 * @property Collection|MstKegiatan[] $mst_kegiatans
 * @property Collection|MstProgramKerja[] $mst_program_kerjas
 *
 * @package App\Models
 */
class MstKegiatan extends Model
{
	protected $table = 'mst_kegiatan';
	protected $primaryKey = 'ID_KEGIATAN';
	public $timestamps = false;

	protected $casts = [
		'MST_ID_KEGIATAN' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'MST_ID_KEGIATAN',
		'DESKRIPSI_KEGIATAN',
		'IS_DELETE'
	];

	public function mst_kegiatan()
	{
		return $this->belongsTo(MstKegiatan::class, 'MST_ID_KEGIATAN');
	}

	public function mst_kegiatans()
	{
		return $this->hasMany(MstKegiatan::class, 'MST_ID_KEGIATAN');
	}

	public function mst_program_kerjas()
	{
		return $this->hasMany(MstProgramKerja::class, 'ID_KEGIATAN');
	}
}
