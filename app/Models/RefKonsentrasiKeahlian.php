<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKonsentrasiKeahlian
 * 
 * @property int $ID_KONSENTRASI_KEAHLIAN
 * @property int|null $ID_PROG_KEAHLIAN
 * @property string|null $NAMA_KONSENTRASI_KEAHLIAN
 * 
 * @property RefProgramKeahlian|null $ref_program_keahlian
 * @property Collection|MstMapel[] $mst_mapels
 *
 * @package App\Models
 */
class RefKonsentrasiKeahlian extends Model
{
	protected $table = 'ref_konsentrasi_keahlian';
	protected $primaryKey = 'ID_KONSENTRASI_KEAHLIAN';
	public $timestamps = false;

	protected $casts = [
		'ID_PROG_KEAHLIAN' => 'int'
	];

	protected $fillable = [
		'ID_PROG_KEAHLIAN',
		'NAMA_KONSENTRASI_KEAHLIAN'
	];

	public function ref_program_keahlian()
	{
		return $this->belongsTo(RefProgramKeahlian::class, 'ID_PROG_KEAHLIAN');
	}

	public function mst_mapels()
	{
		return $this->hasMany(MstMapel::class, 'ID_KONSENTRASI_KEAHLIAN');
	}
}
