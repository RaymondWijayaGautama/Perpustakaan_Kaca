<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstCoa
 * 
 * @property int $ID_MASTER_COA
 * @property int|null $MST_ID_MASTER_COA
 * @property string|null $KODE_COA
 * @property string|null $DESKRIPSI_COA
 * @property bool|null $IS_DELETE
 * 
 * @property MstCoa|null $mst_coa
 * @property Collection|MstCoa[] $mst_coas
 * @property Collection|MstProgramKerja[] $mst_program_kerjas
 *
 * @package App\Models
 */
class MstCoa extends Model
{
	protected $table = 'mst_coa';
	protected $primaryKey = 'ID_MASTER_COA';
	public $timestamps = false;

	protected $casts = [
		'MST_ID_MASTER_COA' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'MST_ID_MASTER_COA',
		'KODE_COA',
		'DESKRIPSI_COA',
		'IS_DELETE'
	];

	public function mst_coa()
	{
		return $this->belongsTo(MstCoa::class, 'MST_ID_MASTER_COA');
	}

	public function mst_coas()
	{
		return $this->hasMany(MstCoa::class, 'MST_ID_MASTER_COA');
	}

	public function mst_program_kerjas()
	{
		return $this->hasMany(MstProgramKerja::class, 'ID_MASTER_COA');
	}
}
