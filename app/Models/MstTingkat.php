<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstTingkat
 * 
 * @property int $ID_TINGKAT
 * @property string|null $NAMA_TINGKATAN
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstKela[] $mst_kelas
 *
 * @package App\Models
 */
class MstTingkat extends Model
{
	protected $table = 'mst_tingkat';
	protected $primaryKey = 'ID_TINGKAT';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_TINGKATAN',
		'IS_DELETE'
	];

	public function mst_kelas()
	{
		return $this->hasMany(MstKela::class, 'ID_TINGKAT');
	}
}
