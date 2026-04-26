<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefTarif
 * 
 * @property int $ID_REF_TARIF
 * @property int|null $ID_JENIS_TARIF
 * @property int|null $ID_TA_ANGGARAN
 * @property string|null $DESKRIPSI_TARIF
 * @property float|null $NOMINAL
 * @property Carbon|null $TGL_PENETAPAN
 * 
 * @property RefJenisTarif|null $ref_jenis_tarif
 * @property RefTahunAnggaran|null $ref_tahun_anggaran
 *
 * @package App\Models
 */
class RefTarif extends Model
{
	protected $table = 'ref_tarif';
	protected $primaryKey = 'ID_REF_TARIF';
	public $timestamps = false;

	protected $casts = [
		'ID_JENIS_TARIF' => 'int',
		'ID_TA_ANGGARAN' => 'int',
		'NOMINAL' => 'float',
		'TGL_PENETAPAN' => 'datetime'
	];

	protected $fillable = [
		'ID_JENIS_TARIF',
		'ID_TA_ANGGARAN',
		'DESKRIPSI_TARIF',
		'NOMINAL',
		'TGL_PENETAPAN'
	];

	public function ref_jenis_tarif()
	{
		return $this->belongsTo(RefJenisTarif::class, 'ID_JENIS_TARIF');
	}

	public function ref_tahun_anggaran()
	{
		return $this->belongsTo(RefTahunAnggaran::class, 'ID_TA_ANGGARAN');
	}
}
