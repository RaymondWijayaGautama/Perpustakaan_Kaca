<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RefPrestasi
 * 
 * @property int $ID_REF_PRESTASI
 * @property string|null $NAMA_PRESTASI
 * @property string|null $JENIS_PRESTASI
 * @property int|null $POIN_PRESTASI
 *
 * @package App\Models
 */
class RefPrestasi extends Model
{
	protected $table = 'ref_prestasi';
	protected $primaryKey = 'ID_REF_PRESTASI';
	public $timestamps = false;

	protected $casts = [
		'POIN_PRESTASI' => 'int'
	];

	protected $fillable = [
		'NAMA_PRESTASI',
		'JENIS_PRESTASI',
		'POIN_PRESTASI'
	];
}
