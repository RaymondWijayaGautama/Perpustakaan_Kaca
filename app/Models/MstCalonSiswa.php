<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstCalonSiswa
 * 
 * @property string $KODE_CALON
 * @property int|null $KODE_TA
 * @property string|null $NAMA_CALON
 * @property string|null $NISN_CALON
 * @property Carbon|null $TGL_LAHIR_CALON
 * @property string|null $GENDER_CALON
 * @property string|null $TEMPAT_LAHIR_CALON
 * @property string|null $NO_HP_CALON
 * @property string|null $GOLDAR_CALON
 * @property string|null $ALAMAT_JALAN_CALON
 * @property string|null $RT_CALON
 * @property string|null $RW_CALON
 * @property string|null $KELURAHAN_CALON
 * @property string|null $KECAMATAN_CALON
 * @property string|null $KOTA_KAB_CALON
 * @property string|null $PROVINSI_CALON
 * @property string|null $KODE_POS_CALON
 * @property string|null $STATUS_TINGGAL
 * @property string|null $NIK_CALON
 * @property string|null $AGAMA_CALON
 * @property string|null $ASAL_SEKOLAH
 * @property string|null $ALAMAT_ASAL_SEKOLAH
 * @property string|null $STATUS_ASAL_SEKOLAH
 * @property string|null $NAMA_AYAH
 * @property string|null $NAMA_IBU
 * @property string|null $NAMA_WALI
 * @property string|null $PEKERJAAN_AYAH
 * @property string|null $PEKERJAAN_IBU
 * @property float|null $PENGHASILAN_AYAH
 * @property float|null $PENGHASILAN_IBU
 * @property float|null $JUMLAH_PENGHASILAN_ORTU
 * @property string|null $JARAK_RUMAH
 * @property string|null $JENIS_TRANSPORTASI
 * @property string|null $ALAMAT_WALI
 * @property string|null $PEKERJAAN_WALI
 * @property string|null $EMAIL_CALON_SISWA
 * @property string|null $PASSWORD_CALON_SISWA
 * @property bool|null $IS_DELETE
 * 
 * @property RefTum|null $ref_tum
 * @property Collection|PrestasiCalonSiswa[] $prestasi_calon_siswas
 * @property Collection|TrPendaftaran[] $tr_pendaftarans
 *
 * @package App\Models
 */
class MstCalonSiswa extends Model
{
	protected $table = 'mst_calon_siswa';
	protected $primaryKey = 'KODE_CALON';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'KODE_TA' => 'int',
		'TGL_LAHIR_CALON' => 'datetime',
		'PENGHASILAN_AYAH' => 'float',
		'PENGHASILAN_IBU' => 'float',
		'JUMLAH_PENGHASILAN_ORTU' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'KODE_TA',
		'NAMA_CALON',
		'NISN_CALON',
		'TGL_LAHIR_CALON',
		'GENDER_CALON',
		'TEMPAT_LAHIR_CALON',
		'NO_HP_CALON',
		'GOLDAR_CALON',
		'ALAMAT_JALAN_CALON',
		'RT_CALON',
		'RW_CALON',
		'KELURAHAN_CALON',
		'KECAMATAN_CALON',
		'KOTA_KAB_CALON',
		'PROVINSI_CALON',
		'KODE_POS_CALON',
		'STATUS_TINGGAL',
		'NIK_CALON',
		'AGAMA_CALON',
		'ASAL_SEKOLAH',
		'ALAMAT_ASAL_SEKOLAH',
		'STATUS_ASAL_SEKOLAH',
		'NAMA_AYAH',
		'NAMA_IBU',
		'NAMA_WALI',
		'PEKERJAAN_AYAH',
		'PEKERJAAN_IBU',
		'PENGHASILAN_AYAH',
		'PENGHASILAN_IBU',
		'JUMLAH_PENGHASILAN_ORTU',
		'JARAK_RUMAH',
		'JENIS_TRANSPORTASI',
		'ALAMAT_WALI',
		'PEKERJAAN_WALI',
		'EMAIL_CALON_SISWA',
		'PASSWORD_CALON_SISWA',
		'IS_DELETE'
	];

	public function ref_tum()
	{
		return $this->belongsTo(RefTum::class, 'KODE_TA');
	}

	public function prestasi_calon_siswas()
	{
		return $this->hasMany(PrestasiCalonSiswa::class, 'KODE_CALON');
	}

	public function tr_pendaftarans()
	{
		return $this->hasMany(TrPendaftaran::class, 'KODE_CALON');
	}
}
