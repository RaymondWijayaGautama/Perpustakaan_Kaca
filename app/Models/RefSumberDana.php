<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefSumberDana
 * 
 * @property int $ID_REF_DANA
 * @property int|null $REF_ID_REF_DANA
 * @property string|null $DESKRIPSI_SUMBER_DANA
 * 
 * @property RefSumberDana|null $ref_sumber_dana
 * @property Collection|DtlProgramKerja[] $dtl_program_kerjas
 * @property Collection|RefSumberDana[] $ref_sumber_danas
 * @property Collection|TrPenerimaan[] $tr_penerimaans
 *
 * @package App\Models
 */
class RefSumberDana extends Model
{
	protected $table = 'ref_sumber_dana';
	protected $primaryKey = 'ID_REF_DANA';
	public $timestamps = false;

	protected $casts = [
		'REF_ID_REF_DANA' => 'int'
	];

	protected $fillable = [
		'REF_ID_REF_DANA',
		'DESKRIPSI_SUMBER_DANA'
	];

	public function ref_sumber_dana()
	{
		return $this->belongsTo(RefSumberDana::class, 'REF_ID_REF_DANA');
	}

	public function dtl_program_kerjas()
	{
		return $this->hasMany(DtlProgramKerja::class, 'ID_REF_DANA');
	}

	public function ref_sumber_danas()
	{
		return $this->hasMany(RefSumberDana::class, 'REF_ID_REF_DANA');
	}

	public function tr_penerimaans()
	{
		return $this->hasMany(TrPenerimaan::class, 'ID_REF_DANA');
	}
}
