<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefIntegrita
 * 
 * @property int $ID_REF_INTEGRITAS
 * @property int|null $REF_ID_REF_INTEGRITAS
 * 
 * @property RefIntegrita|null $ref_integrita
 * @property Collection|RaporIntegrita[] $rapor_integritas
 * @property Collection|RefIntegrita[] $ref_integritas
 *
 * @package App\Models
 */
class RefIntegrita extends Model
{
	protected $table = 'ref_integritas';
	protected $primaryKey = 'ID_REF_INTEGRITAS';
	public $timestamps = false;

	protected $casts = [
		'REF_ID_REF_INTEGRITAS' => 'int'
	];

	protected $fillable = [
		'REF_ID_REF_INTEGRITAS'
	];

	public function ref_integrita()
	{
		return $this->belongsTo(RefIntegrita::class, 'REF_ID_REF_INTEGRITAS');
	}

	public function rapor_integritas()
	{
		return $this->hasMany(RaporIntegrita::class, 'ID_REF_INTEGRITAS');
	}

	public function ref_integritas()
	{
		return $this->hasMany(RefIntegrita::class, 'REF_ID_REF_INTEGRITAS');
	}
}
