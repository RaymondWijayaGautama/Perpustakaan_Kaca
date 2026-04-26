<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefProvinsi
 * 
 * @property int $ID_PROVINSI
 * @property string|null $NAMA_PROV
 * 
 * @property Collection|RefKotaKab[] $ref_kota_kabs
 *
 * @package App\Models
 */
class RefProvinsi extends Model
{
	protected $table = 'ref_provinsi';
	protected $primaryKey = 'ID_PROVINSI';
	public $timestamps = false;

	protected $fillable = [
		'NAMA_PROV'
	];

	public function ref_kota_kabs()
	{
		return $this->hasMany(RefKotaKab::class, 'ID_PROVINSI');
	}
}
