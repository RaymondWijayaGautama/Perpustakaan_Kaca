<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrCicilan
 * 
 * @property int $ID_TR_CICILAN
 * @property int|null $ID_PEMBAYARAN
 * @property Carbon|null $TGL_CICILAN
 * @property float|null $JUMLAH_CICILAN
 * @property int|null $CICILAN_KE
 * 
 * @property TrPembayaran|null $tr_pembayaran
 *
 * @package App\Models
 */
class TrCicilan extends Model
{
	protected $table = 'tr_cicilan';
	protected $primaryKey = 'ID_TR_CICILAN';
	public $timestamps = false;

	protected $casts = [
		'ID_PEMBAYARAN' => 'int',
		'TGL_CICILAN' => 'datetime',
		'JUMLAH_CICILAN' => 'float',
		'CICILAN_KE' => 'int'
	];

	protected $fillable = [
		'ID_PEMBAYARAN',
		'TGL_CICILAN',
		'JUMLAH_CICILAN',
		'CICILAN_KE'
	];

	public function tr_pembayaran()
	{
		return $this->belongsTo(TrPembayaran::class, 'ID_PEMBAYARAN');
	}
}
