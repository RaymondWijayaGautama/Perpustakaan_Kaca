<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJenisTarif
 * 
 * @property int $ID_JENIS_TARIF
 * @property string|null $DESKRIPSI_JENIS_TARIF
 * 
 * @property Collection|RefTarif[] $ref_tarifs
 *
 * @package App\Models
 */
class RefJenisTarif extends Model
{
	protected $table = 'ref_jenis_tarif';
	protected $primaryKey = 'ID_JENIS_TARIF';
	public $timestamps = false;

	protected $fillable = [
		'DESKRIPSI_JENIS_TARIF'
	];

	public function ref_tarifs()
	{
		return $this->hasMany(RefTarif::class, 'ID_JENIS_TARIF');
	}
}
