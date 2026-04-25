<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPenerimaan
 * 
 * @property int $ID_TR_PENERIMAAN
 * @property int|null $ID_REF_PENERIMAAN
 * @property int|null $ID_REF_DANA
 * @property string|null $DESKRIPSI_TR_PENERIMAAN
 * @property Carbon|null $TANGGAL_TR_PENERIMAAN
 * @property float|null $JUMLAH_TR_PENERIMAAN
 * @property string|null $NIP_PENERIMA
 * 
 * @property RefPenerimaan|null $ref_penerimaan
 * @property RefSumberDana|null $ref_sumber_dana
 *
 * @package App\Models
 */
class TrPenerimaan extends Model
{
	protected $table = 'tr_penerimaan';
	protected $primaryKey = 'ID_TR_PENERIMAAN';
	public $timestamps = false;

	protected $casts = [
		'ID_REF_PENERIMAAN' => 'int',
		'ID_REF_DANA' => 'int',
		'TANGGAL_TR_PENERIMAAN' => 'datetime',
		'JUMLAH_TR_PENERIMAAN' => 'float'
	];

	protected $fillable = [
		'ID_REF_PENERIMAAN',
		'ID_REF_DANA',
		'DESKRIPSI_TR_PENERIMAAN',
		'TANGGAL_TR_PENERIMAAN',
		'JUMLAH_TR_PENERIMAAN',
		'NIP_PENERIMA'
	];

	public function ref_penerimaan()
	{
		return $this->belongsTo(RefPenerimaan::class, 'ID_REF_PENERIMAAN');
	}

	public function ref_sumber_dana()
	{
		return $this->belongsTo(RefSumberDana::class, 'ID_REF_DANA');
	}
}
