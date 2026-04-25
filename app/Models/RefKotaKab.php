<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKotaKab
 * 
 * @property int $ID_KOTA_KAB
 * @property int|null $ID_PROVINSI
 * @property string|null $NAMA_KOTA_KAB
 * 
 * @property RefProvinsi|null $ref_provinsi
 * @property Collection|RefKecamatan[] $ref_kecamatans
 *
 * @package App\Models
 */
class RefKotaKab extends Model
{
	protected $table = 'ref_kota_kab';
	protected $primaryKey = 'ID_KOTA_KAB';
	public $timestamps = false;

	protected $casts = [
		'ID_PROVINSI' => 'int'
	];

	protected $fillable = [
		'ID_PROVINSI',
		'NAMA_KOTA_KAB'
	];

	public function ref_provinsi()
	{
		return $this->belongsTo(RefProvinsi::class, 'ID_PROVINSI');
	}

	public function ref_kecamatans()
	{
		return $this->hasMany(RefKecamatan::class, 'ID_KOTA_KAB');
	}
}
