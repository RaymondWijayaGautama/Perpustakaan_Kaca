<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RefPelanggaran
 * 
 * @property int $ID_REF_PELANGGARAN
 * @property string|null $NAMA_PELANGGARAN
 * @property string|null $JENIS_PELANGGARAN
 * @property int|null $POIN_PELANGGARAN
 *
 * @package App\Models
 */
class RefPelanggaran extends Model
{
	protected $table = 'ref_pelanggaran';
	protected $primaryKey = 'ID_REF_PELANGGARAN';
	public $timestamps = false;

	protected $casts = [
		'POIN_PELANGGARAN' => 'int'
	];

	protected $fillable = [
		'NAMA_PELANGGARAN',
		'JENIS_PELANGGARAN',
		'POIN_PELANGGARAN'
	];
}
