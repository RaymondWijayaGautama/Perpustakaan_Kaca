<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPendaftaran
 * 
 * @property int $ID_PENDAFTARAN
 * @property string|null $KODE_CALON
 * @property int|null $KODE_TA
 * @property string|null $NO_PENDAFTARAN
 * @property Carbon|null $TGL_DAFTAR
 * @property string|null $BUKTI_BAYAR
 * @property float|null $BAYAR_SPS
 * @property float|null $NILAI_BHS_INDO
 * @property float|null $NILAI_BHS_ING
 * @property float|null $NILAI_MTK
 * @property float|null $NILAI_IPA
 * @property string|null $UKURAN_BAJU
 * @property string|null $PILIHAN_PRODI
 * @property string|null $SUMBER_INFO
 * @property string|null $ALASAN_DAFTAR
 * @property string|null $ALASAN_PRODI
 * @property string|null $HOBI
 * @property string|null $CITA_CITA
 * @property string|null $ORANG_DIHORMATI
 * @property string|null $ORGANISASI_DIIKUTI
 * @property int|null $JALUR_PENDAF
 * @property string|null $STATUS_PENDAF
 * @property string|null $STATUS_VERIF_BAYAR
 * 
 * @property MstCalonSiswa|null $mst_calon_siswa
 * @property RefTum|null $ref_tum
 * @property Collection|DokumenCalonSiswa[] $dokumen_calon_siswas
 * @property Collection|MstSiswa[] $mst_siswas
 * @property Collection|TrWawancara[] $tr_wawancaras
 *
 * @package App\Models
 */
class TrPendaftaran extends Model
{
	protected $table = 'tr_pendaftaran';
	protected $primaryKey = 'ID_PENDAFTARAN';
	public $timestamps = false;

	protected $casts = [
		'KODE_TA' => 'int',
		'TGL_DAFTAR' => 'datetime',
		'BAYAR_SPS' => 'float',
		'NILAI_BHS_INDO' => 'float',
		'NILAI_BHS_ING' => 'float',
		'NILAI_MTK' => 'float',
		'NILAI_IPA' => 'float',
		'JALUR_PENDAF' => 'int'
	];

	protected $fillable = [
		'KODE_CALON',
		'KODE_TA',
		'NO_PENDAFTARAN',
		'TGL_DAFTAR',
		'BUKTI_BAYAR',
		'BAYAR_SPS',
		'NILAI_BHS_INDO',
		'NILAI_BHS_ING',
		'NILAI_MTK',
		'NILAI_IPA',
		'UKURAN_BAJU',
		'PILIHAN_PRODI',
		'SUMBER_INFO',
		'ALASAN_DAFTAR',
		'ALASAN_PRODI',
		'HOBI',
		'CITA_CITA',
		'ORANG_DIHORMATI',
		'ORGANISASI_DIIKUTI',
		'JALUR_PENDAF',
		'STATUS_PENDAF',
		'STATUS_VERIF_BAYAR'
	];

	public function mst_calon_siswa()
	{
		return $this->belongsTo(MstCalonSiswa::class, 'KODE_CALON');
	}

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function dokumen_calon_siswas()
	{
		return $this->hasMany(DokumenCalonSiswa::class, 'ID_PENDAFTARAN');
	}

	public function mst_siswas()
	{
		return $this->hasMany(MstSiswa::class, 'ID_PENDAFTARAN');
	}

	public function tr_wawancaras()
	{
		return $this->hasMany(TrWawancara::class, 'ID_PENDAFTARAN');
	}
}
