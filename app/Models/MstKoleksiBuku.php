<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKoleksiBuku
 * 
 * @property string $ISBN
 * @property int|null $ID_REF_KOLEKSI
 * @property string|null $JUDUL_KOLEKSI
 * @property string|null $PENGARANG
 * @property string|null $PENERBIT
 * @property string|null $TAHUN
 * @property int|null $NB_KOLEKSI
 * @property Carbon|null $TGL_MASUK_KOLEKSI
 * @property int|null $JUMLAH_EKSEMPLAR
 * @property int|null $JUMLAH_HALAMAN
 * @property string|null $UKURAN_BUKU
 * @property string|null $BIBLIOGRAFI
 * @property int|null $INDEKS_AWAL_AKHIR
 * @property string|null $KETERANGAN_BUKU
 * @property string|null $NO_RAK_BUKU
 * @property bool|null $IS_DELETE
 * 
 * @property RefKoleksi|null $ref_koleksi
 * @property Collection|CpKoleksi[] $cp_koleksis
 *
 * @package App\Models
 */
class MstKoleksiBuku extends Model
{
	protected $table = 'mst_koleksi_buku';
	protected $primaryKey = 'ISBN';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID_REF_KOLEKSI' => 'int',
		'NB_KOLEKSI' => 'int',
		'TGL_MASUK_KOLEKSI' => 'datetime',
		'JUMLAH_EKSEMPLAR' => 'int',
		'JUMLAH_HALAMAN' => 'int',
		'INDEKS_AWAL_AKHIR' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_REF_KOLEKSI',
		'JUDUL_KOLEKSI',
		'PENGARANG',
		'PENERBIT',
		'TAHUN',
		'NB_KOLEKSI',
		'TGL_MASUK_KOLEKSI',
		'JUMLAH_EKSEMPLAR',
		'JUMLAH_HALAMAN',
		'UKURAN_BUKU',
		'BIBLIOGRAFI',
		'INDEKS_AWAL_AKHIR',
		'KETERANGAN_BUKU',
		'NO_RAK_BUKU',
		'IS_DELETE'
	];

	public function ref_koleksi()
	{
		return $this->belongsTo(RefKoleksi::class, 'ID_REF_KOLEKSI');
	}

	public function cp_koleksis()
	{
		return $this->hasMany(CpKoleksi::class, 'ISBN');
	}
}
