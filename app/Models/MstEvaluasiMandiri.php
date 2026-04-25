<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstEvaluasiMandiri
 * 
 * @property int $ID_MST_EVALUASI_MANDIRI
 * @property string|null $NAMA_KOMPETENSI_EVALUASI
 * @property bool|null $IS_VALID_EVAL
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|DtEvaluasiMandiri[] $dt_evaluasi_mandiris
 *
 * @package App\Models
 */
class MstEvaluasiMandiri extends Model
{
	protected $table = 'mst_evaluasi_mandiri';
	protected $primaryKey = 'ID_MST_EVALUASI_MANDIRI';
	public $timestamps = false;

	protected $casts = [
		'IS_VALID_EVAL' => 'bool',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_KOMPETENSI_EVALUASI',
		'IS_VALID_EVAL',
		'IS_DELETE'
	];

	public function dt_evaluasi_mandiris()
	{
		return $this->hasMany(DtEvaluasiMandiri::class, 'ID_MST_EVALUASI_MANDIRI');
	}
}
