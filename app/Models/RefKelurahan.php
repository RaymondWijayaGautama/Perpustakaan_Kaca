<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RefKelurahan
 * 
 * @property int $ID_KELURAHAN
 * @property int|null $ID_KECAMATAN
 * @property string|null $NAMA_KEL
 * 
 * @property RefKecamatan|null $ref_kecamatan
 *
 * @package App\Models
 */
class RefKelurahan extends Model
{
	protected $table = 'ref_kelurahan';
	protected $primaryKey = 'ID_KELURAHAN';
	public $timestamps = false;

	protected $casts = [
		'ID_KECAMATAN' => 'int'
	];

	protected $fillable = [
		'ID_KECAMATAN',
		'NAMA_KEL'
	];

	public function ref_kecamatan()
	{
		return $this->belongsTo(RefKecamatan::class, 'ID_KECAMATAN');
	}
}
