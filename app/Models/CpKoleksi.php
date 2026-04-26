<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CpKoleksi
 * 
 * @property int $ID_CP_KOLEKSI
 * @property string|null $ISBN	
 * @property int|null $ID_MST_LAPORAN
 * @property string|null $STATUS_BUKU
 * 
 * @property MstKoleksiBuku|null $mst_koleksi_buku
 * @property MstKoleksiLaporan|null $mst_koleksi_laporan
 * @property Collection|TrPeminjaman[] $tr_peminjamen
 * @property Collection|TrPemusnahanBuku[] $tr_pemusnahan_bukus
 *
 * @package App\Models
 */
class CpKoleksi extends Model
{
	protected $table = 'cp_koleksi';
	protected $primaryKey = 'ID_CP_KOLEKSI';
	public $timestamps = false;

	protected $casts = [
		'ID_MST_LAPORAN' => 'int'
	];

	protected $fillable = [
		'ISBN',
		'ID_MST_LAPORAN',
		'STATUS_BUKU'
	];

	public function mst_koleksi_buku()
	{
		return $this->belongsTo(MstKoleksiBuku::class, 'ISBN');
	}

	public function mst_koleksi_laporan()
	{
		return $this->belongsTo(MstKoleksiLaporan::class, 'ID_MST_LAPORAN');
	}

	public function tr_peminjamen()
	{
		return $this->hasMany(TrPeminjaman::class, 'ID_CP_KOLEKSI');
	}

	public function tr_pemusnahan_bukus()
	{
		return $this->hasMany(TrPemusnahanBuku::class, 'ID_CP_KOLEKSI');
	}
}
