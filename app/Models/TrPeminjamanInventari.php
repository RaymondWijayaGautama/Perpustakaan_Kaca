<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPeminjamanInventari
 * 
 * @property int $ID_TR_PEMINJAMAN_INV
 * @property string|null $NIP_KARYAWAN
 * @property Carbon|null $TGL_MULAI_PINJAM
 * @property Carbon|null $TGL_SELESAI_PINJAM
 * @property string|null $STATUS_PEMINJAMAN_INV
 * @property string|null $KETERANGAN_PEMINJAMAN_INV
 * @property string|null $NIP_VALIDATOR_PEMINJAMAN
 * 
 * @property MstKaryawan|null $mst_karyawan
 * @property Collection|DtPeminjamanInventari[] $dt_peminjaman_inventaris
 *
 * @package App\Models
 */
class TrPeminjamanInventari extends Model
{
	protected $table = 'tr_peminjaman_inventaris';
	protected $primaryKey = 'ID_TR_PEMINJAMAN_INV';
	public $timestamps = false;

	protected $casts = [
		'TGL_MULAI_PINJAM' => 'datetime',
		'TGL_SELESAI_PINJAM' => 'datetime'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'TGL_MULAI_PINJAM',
		'TGL_SELESAI_PINJAM',
		'STATUS_PEMINJAMAN_INV',
		'KETERANGAN_PEMINJAMAN_INV',
		'NIP_VALIDATOR_PEMINJAMAN'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function dt_peminjaman_inventaris()
	{
		return $this->hasMany(DtPeminjamanInventari::class, 'ID_TR_PEMINJAMAN_INV');
	}
}
