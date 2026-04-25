<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPeminjaman
 * 
 * @property int $ID_PEMINJAMAN
 * @property int|null $ID_SISWA_TETAP
 * @property int|null $ID_CP_KOLEKSI
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_PINJAM
 * @property Carbon|null $TGL_HARUS_KEMBALI
 * @property Carbon|null $TGL_KEMBALI
 * @property string|null $STATUS_PEMINJAMAN
 * @property string|null $KONDISI_BUKU
 * @property string|null $KETERANGAN_PEMINJAMAN
 * @property float|null $DENDA_PEMINJAMAN
 * 
 * @property MstSiswa|null $mst_siswa
 * @property CpKoleksi|null $cp_koleksi
 * @property MstKaryawan|null $mst_karyawan
 *
 * @package App\Models
 */
class TrPeminjaman extends Model
{
	protected $table = 'tr_peminjaman';
	protected $primaryKey = 'ID_PEMINJAMAN';
	public $timestamps = false;

	protected $casts = [
		'ID_SISWA_TETAP' => 'int',
		'ID_CP_KOLEKSI' => 'int',
		'TGL_PINJAM' => 'datetime',
		'TGL_HARUS_KEMBALI' => 'datetime',
		'TGL_KEMBALI' => 'datetime',
		'DENDA_PEMINJAMAN' => 'float'
	];

	protected $fillable = [
		'ID_SISWA_TETAP',
		'ID_CP_KOLEKSI',
		'NIP_KARYAWAN',
		'TGL_PINJAM',
		'TGL_HARUS_KEMBALI',
		'TGL_KEMBALI',
		'STATUS_PEMINJAMAN',
		'KONDISI_BUKU',
		'KETERANGAN_PEMINJAMAN',
		'DENDA_PEMINJAMAN'
	];

	public function mst_siswa()
	{
		return $this->belongsTo(MstSiswa::class, 'ID_SISWA_TETAP');
	}

	public function cp_koleksi()
	{
		return $this->belongsTo(CpKoleksi::class, 'ID_CP_KOLEKSI');
	}

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}
}
