<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefTan
 * 
 * @property int $ID_TAN
 * @property string|null $TAHUN
 * @property bool|null $IS_CURRENT
 * @property string|null $DESKRIPSI_TAN
 * 
 * @property Collection|MstProgramKerja[] $mst_program_kerjas
 *
 * @package App\Models
 */
class RefTan extends Model
{
	protected $table = 'ref_tan';
	protected $primaryKey = 'ID_TAN';
	public $timestamps = false;

	protected $casts = [
		'IS_CURRENT' => 'bool'
	];

	protected $fillable = [
		'TAHUN',
		'IS_CURRENT',
		'DESKRIPSI_TAN'
	];

	public function mst_program_kerjas()
	{
		return $this->hasMany(MstProgramKerja::class, 'ID_TAN');
	}
}
