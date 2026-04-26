<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefPenerimaan
 * 
 * @property int $ID_REF_PENERIMAAN
 * @property int|null $REF_ID_REF_PENERIMAAN
 * @property string|null $DESKRIPSI_REF_PENERIMAAN
 * 
 * @property RefPenerimaan|null $ref_penerimaan
 * @property Collection|RefPenerimaan[] $ref_penerimaans
 * @property Collection|TrPenerimaan[] $tr_penerimaans
 *
 * @package App\Models
 */
class RefPenerimaan extends Model
{
	protected $table = 'ref_penerimaan';
	protected $primaryKey = 'ID_REF_PENERIMAAN';
	public $timestamps = false;

	protected $casts = [
		'REF_ID_REF_PENERIMAAN' => 'int'
	];

	protected $fillable = [
		'REF_ID_REF_PENERIMAAN',
		'DESKRIPSI_REF_PENERIMAAN'
	];

	public function ref_penerimaan()
	{
		return $this->belongsTo(RefPenerimaan::class, 'REF_ID_REF_PENERIMAAN');
	}

	public function ref_penerimaans()
	{
		return $this->hasMany(RefPenerimaan::class, 'REF_ID_REF_PENERIMAAN');
	}

	public function tr_penerimaans()
	{
		return $this->hasMany(TrPenerimaan::class, 'ID_REF_PENERIMAAN');
	}
}
