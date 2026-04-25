<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPembayaran
 * 
 * @property int $ID_PEMBAYARAN
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $KODE_TA
 * @property int|null $ID_JENIS_PEMBAYARAN
 * @property int|null $ID_TAGIHAN_SISWA
 * @property int|null $REF_ID_JENIS_PEMBAYARAN
 * @property Carbon|null $TGL_BAYAR
 * @property float|null $JUMLAH_BAYAR
 * @property string|null $LINK_BUKTI_BAYAR
 * @property string|null $NIP_VALIDATOR_PEMBAYARAN
 * 
 * @property MstSiswa|null $mst_siswa
 * @property RefTum|null $ref_tum
 * @property RefJenisPembayaran|null $ref_jenis_pembayaran
 * @property TagihanSiswa|null $tagihan_siswa
 * @property Collection|TrCicilan[] $tr_cicilans
 *
 * @package App\Models
 */
class TrPembayaran extends Model
{
	protected $table = 'tr_pembayaran';
	protected $primaryKey = 'ID_PEMBAYARAN';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'KODE_TA' => 'int',
		'ID_JENIS_PEMBAYARAN' => 'int',
		'ID_TAGIHAN_SISWA' => 'int',
		'REF_ID_JENIS_PEMBAYARAN' => 'int',
		'TGL_BAYAR' => 'datetime',
		'JUMLAH_BAYAR' => 'float'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'KODE_TA',
		'ID_JENIS_PEMBAYARAN',
		'ID_TAGIHAN_SISWA',
		'REF_ID_JENIS_PEMBAYARAN',
		'TGL_BAYAR',
		'JUMLAH_BAYAR',
		'LINK_BUKTI_BAYAR',
		'NIP_VALIDATOR_PEMBAYARAN'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function ref_jenis_pembayaran()
	{
		return $this->belongsTo(RefJenisPembayaran::class, 'REF_ID_JENIS_PEMBAYARAN');
	}

	public function tagihan_siswa()
	{
		return $this->belongsTo(TagihanSiswa::class, 'ID_TAGIHAN_SISWA');
	}

	public function tr_cicilans()
	{
		return $this->hasMany(TrCicilan::class, 'ID_PEMBAYARAN');
	}
}
