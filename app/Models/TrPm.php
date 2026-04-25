<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPm
 * 
 * @property int $ID_PM
 * @property int|null $ID_PROGRAM_KERJA
 * @property int|null $ID_REF_PM
 * @property Carbon|null $TGL_PM
 * @property string|null $DESKRIPSI_TR_PM
 * 
 * @property MstProgramKerja|null $mst_program_kerja
 * @property RefPm|null $ref_pm
 *
 * @package App\Models
 */
class TrPm extends Model
{
	protected $table = 'tr_pm';
	protected $primaryKey = 'ID_PM';
	public $timestamps = false;

	protected $casts = [
		'ID_PROGRAM_KERJA' => 'int',
		'ID_REF_PM' => 'int',
		'TGL_PM' => 'datetime'
	];

	protected $fillable = [
		'ID_PROGRAM_KERJA',
		'ID_REF_PM',
		'TGL_PM',
		'DESKRIPSI_TR_PM'
	];

	public function mst_program_kerja()
	{
		return $this->belongsTo(MstProgramKerja::class, 'ID_PROGRAM_KERJA');
	}

	public function ref_pm()
	{
		return $this->belongsTo(RefPm::class, 'ID_REF_PM');
	}
}
