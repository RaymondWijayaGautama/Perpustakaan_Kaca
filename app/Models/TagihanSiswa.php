<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TagihanSiswa
 * 
 * @property int $ID_TAGIHAN_SISWA
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $ID_JENIS_PEMBAYARAN
 * @property string|null $BULAN_TAGIHAN_SISWA
 * @property string|null $TAHUN_TAGIHAN_SISWA
 * @property float|null $JUMLAH_TAGIHAN_SISWA
 * @property string|null $STATUS_TAGIHAN_SISWA
 * @property Carbon|null $DUEDATETIME_TAGIHAN_SISWA
 * 
 * @property MstSiswa|null $mst_siswa
 * @property RefJenisPembayaran|null $ref_jenis_pembayaran
 * @property Collection|TrPembayaran[] $tr_pembayarans
 *
 * @package App\Models
 */
class TagihanSiswa extends Model
{
	protected $table = 'tagihan_siswa';
	protected $primaryKey = 'ID_TAGIHAN_SISWA';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'ID_JENIS_PEMBAYARAN' => 'int',
		'JUMLAH_TAGIHAN_SISWA' => 'float',
		'DUEDATETIME_TAGIHAN_SISWA' => 'datetime'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'ID_JENIS_PEMBAYARAN',
		'BULAN_TAGIHAN_SISWA',
		'TAHUN_TAGIHAN_SISWA',
		'JUMLAH_TAGIHAN_SISWA',
		'STATUS_TAGIHAN_SISWA',
		'DUEDATETIME_TAGIHAN_SISWA'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function ref_jenis_pembayaran()
	{
		return $this->belongsTo(RefJenisPembayaran::class, 'ID_JENIS_PEMBAYARAN');
	}

	public function tr_pembayarans()
	{
		return $this->hasMany(TrPembayaran::class, 'ID_TAGIHAN_SISWA');
	}
}
