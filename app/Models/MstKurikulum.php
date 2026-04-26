<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKurikulum
 * 
 * @property int $ID_KURIKULUM
 * @property string|null $NAMA_KURIKULUM
 * @property string|null $NO_SK_PENETAPAN
 * @property string|null $STATUS_KURIKULUM
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstMapel[] $mst_mapels
 * @property Collection|RefProgramKeahlian[] $ref_program_keahlians
 *
 * @package App\Models
 */
class MstKurikulum extends Model
{
	protected $table = 'mst_kurikulum';
	protected $primaryKey = 'ID_KURIKULUM';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_KURIKULUM',
		'NO_SK_PENETAPAN',
		'STATUS_KURIKULUM',
		'IS_DELETE'
	];

	public function mst_mapels()
	{
		return $this->hasMany(MstMapel::class, 'ID_KURIKULUM');
	}

	public function ref_program_keahlians()
	{
		return $this->hasMany(RefProgramKeahlian::class, 'ID_KURIKULUM');
	}
}
