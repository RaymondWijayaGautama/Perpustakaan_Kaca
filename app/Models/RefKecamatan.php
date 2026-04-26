<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKecamatan
 * 
 * @property int $ID_KECAMATAN
 * @property int|null $ID_KOTA_KAB
 * @property string|null $NAMA_KEC
 * 
 * @property RefKotaKab|null $ref_kota_kab
 * @property Collection|RefKelurahan[] $ref_kelurahans
 *
 * @package App\Models
 */
class RefKecamatan extends Model
{
	protected $table = 'ref_kecamatan';
	protected $primaryKey = 'ID_KECAMATAN';
	public $timestamps = false;

	protected $casts = [
		'ID_KOTA_KAB' => 'int'
	];

	protected $fillable = [
		'ID_KOTA_KAB',
		'NAMA_KEC'
	];

	public function ref_kota_kab()
	{
		return $this->belongsTo(RefKotaKab::class, 'ID_KOTA_KAB');
	}

	public function ref_kelurahans()
	{
		return $this->hasMany(RefKelurahan::class, 'ID_KECAMATAN');
	}
}
