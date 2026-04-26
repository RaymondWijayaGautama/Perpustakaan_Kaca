<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefProgramKeahlian
 * 
 * @property int $ID_PROG_KEAHLIAN
 * @property int|null $ID_KURIKULUM
 * @property string|null $NAMA_PROG_KEAHLIAN
 * 
 * @property MstKurikulum|null $mst_kurikulum
 * @property Collection|MstMapel[] $mst_mapels
 * @property Collection|RefKonsentrasiKeahlian[] $ref_konsentrasi_keahlians
 *
 * @package App\Models
 */
class RefProgramKeahlian extends Model
{
	protected $table = 'ref_program_keahlian';
	protected $primaryKey = 'ID_PROG_KEAHLIAN';
	public $timestamps = false;

	protected $casts = [
		'ID_KURIKULUM' => 'int'
	];

	protected $fillable = [
		'ID_KURIKULUM',
		'NAMA_PROG_KEAHLIAN'
	];

	public function mst_kurikulum()
	{
		return $this->belongsTo(MstKurikulum::class, 'ID_KURIKULUM');
	}

	public function mst_mapels()
	{
		return $this->hasMany(MstMapel::class, 'ID_PROG_KEAHLIAN');
	}

	public function ref_konsentrasi_keahlians()
	{
		return $this->hasMany(RefKonsentrasiKeahlian::class, 'ID_PROG_KEAHLIAN');
	}
}
