<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPrestasiPelanggaranSiswa
 * 
 * @property int $ID_PRESTASI_PELANGGARAN_SISWA
 * @property int|null $KODE_TA
 * @property int|null $ID_SISWA_TERCATAT
 * @property string|null $NAMA_PRESTASI_SISWA
 * @property int|null $POIN_PRESTASI_SISWA
 * @property string|null $NAMA_PELANGGARAN_SISWA
 * @property int|null $POIN_PELANGGARAN
 * 
 * @property RefTum|null $ref_tum
 *
 * @package App\Models
 */
class TrPrestasiPelanggaranSiswa extends Model
{
	protected $table = 'tr_prestasi_pelanggaran_siswa';
	protected $primaryKey = 'ID_PRESTASI_PELANGGARAN_SISWA';
	public $timestamps = false;

	protected $casts = [
		'KODE_TA' => 'int',
		'ID_SISWA_TERCATAT' => 'int',
		'POIN_PRESTASI_SISWA' => 'int',
		'POIN_PELANGGARAN' => 'int'
	];

	protected $fillable = [
		'KODE_TA',
		'ID_SISWA_TERCATAT',
		'NAMA_PRESTASI_SISWA',
		'POIN_PRESTASI_SISWA',
		'NAMA_PELANGGARAN_SISWA',
		'POIN_PELANGGARAN'
	];

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}
}
