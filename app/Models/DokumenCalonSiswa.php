<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DokumenCalonSiswa
 * 
 * @property int $ID_DOKUMEN
 * @property int|null $ID_PENDAFTARAN
 * @property string|null $NAMA_DOKUMEN
 * @property string|null $JENIS_DOKUMEN
 * @property string|null $LINK_DOKUMEN_CALON
 * @property string|null $STATUS_VERIF_DOKUMEN
 * 
 * @property TrPendaftaran|null $tr_pendaftaran
 *
 * @package App\Models
 */
class DokumenCalonSiswa extends Model
{
	protected $table = 'dokumen_calon_siswa';
	protected $primaryKey = 'ID_DOKUMEN';
	public $timestamps = false;

	protected $casts = [
		'ID_PENDAFTARAN' => 'int'
	];

	protected $fillable = [
		'ID_PENDAFTARAN',
		'NAMA_DOKUMEN',
		'JENIS_DOKUMEN',
		'LINK_DOKUMEN_CALON',
		'STATUS_VERIF_DOKUMEN'
	];

	public function tr_pendaftaran()
	{
		return $this->belongsTo(TrPendaftaran::class, 'ID_PENDAFTARAN');
	}
}
