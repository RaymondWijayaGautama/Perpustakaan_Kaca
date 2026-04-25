<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtEvaluasiMandiri
 * 
 * @property int $ID_DT_EVALUASI_MANDIRI
 * @property int|null $ID_MST_EVALUASI_MANDIRI
 * @property int|null $ID_TR_EVALUASI_MANDIRI
 * @property string|null $NILAI_EVALUASI_MANDIRI
 * @property string|null $CATATAN_KHUSUS_EVALUASI
 * @property string|null $REKOMENDASI_EVALUASI
 * @property string|null $TINDAKLANJUT_EVALUASI
 * @property string|null $KETERANGAN_EVALUASI
 * 
 * @property MstEvaluasiMandiri|null $mst_evaluasi_mandiri
 * @property TrEvaluasiMandiri|null $tr_evaluasi_mandiri
 *
 * @package App\Models
 */
class DtEvaluasiMandiri extends Model
{
	protected $table = 'dt_evaluasi_mandiri';
	protected $primaryKey = 'ID_DT_EVALUASI_MANDIRI';
	public $timestamps = false;

	protected $casts = [
		'ID_MST_EVALUASI_MANDIRI' => 'int',
		'ID_TR_EVALUASI_MANDIRI' => 'int'
	];

	protected $fillable = [
		'ID_MST_EVALUASI_MANDIRI',
		'ID_TR_EVALUASI_MANDIRI',
		'NILAI_EVALUASI_MANDIRI',
		'CATATAN_KHUSUS_EVALUASI',
		'REKOMENDASI_EVALUASI',
		'TINDAKLANJUT_EVALUASI',
		'KETERANGAN_EVALUASI'
	];

	public function mst_evaluasi_mandiri()
	{
		return $this->belongsTo(MstEvaluasiMandiri::class, 'ID_MST_EVALUASI_MANDIRI');
	}

	public function tr_evaluasi_mandiri()
	{
		return $this->belongsTo(TrEvaluasiMandiri::class, 'ID_TR_EVALUASI_MANDIRI');
	}
}
