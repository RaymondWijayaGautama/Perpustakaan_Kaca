<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstProgramKerja
 * 
 * @property int $ID_PROGRAM_KERJA
 * @property int|null $ID_TA_ANGGARAN
 * @property int|null $ID_UNIT
 * @property int|null $ID_TAN
 * @property int|null $ID_MASTER_COA
 * @property int|null $ID_KEGIATAN
 * @property float|null $NOMINAL
 * @property string|null $INDIKATOR
 * @property string|null $SASARAN
 * @property Carbon|null $WAKTU_AWAL
 * @property Carbon|null $WAKTU_AKHIR
 * @property string|null $KELUARAN_PROGKER
 * @property string|null $PROGRAM_KERJA
 * @property string|null $NIP_PENANGGUNG_JAWAB
 * @property bool|null $IS_DELETE
 * 
 * @property RefTahunAnggaran|null $ref_tahun_anggaran
 * @property MstUnit|null $mst_unit
 * @property RefTan|null $ref_tan
 * @property MstCoa|null $mst_coa
 * @property MstKegiatan|null $mst_kegiatan
 * @property Collection|DtlProgramKerja[] $dtl_program_kerjas
 * @property Collection|FpdAnggaran[] $fpd_anggarans
 * @property Collection|TrPm[] $tr_pms
 *
 * @package App\Models
 */
class MstProgramKerja extends Model
{
	protected $table = 'mst_program_kerja';
	protected $primaryKey = 'ID_PROGRAM_KERJA';
	public $timestamps = false;

	protected $casts = [
		'ID_TA_ANGGARAN' => 'int',
		'ID_UNIT' => 'int',
		'ID_TAN' => 'int',
		'ID_MASTER_COA' => 'int',
		'ID_KEGIATAN' => 'int',
		'NOMINAL' => 'float',
		'WAKTU_AWAL' => 'datetime',
		'WAKTU_AKHIR' => 'datetime',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_TA_ANGGARAN',
		'ID_UNIT',
		'ID_TAN',
		'ID_MASTER_COA',
		'ID_KEGIATAN',
		'NOMINAL',
		'INDIKATOR',
		'SASARAN',
		'WAKTU_AWAL',
		'WAKTU_AKHIR',
		'KELUARAN_PROGKER',
		'PROGRAM_KERJA',
		'NIP_PENANGGUNG_JAWAB',
		'IS_DELETE'
	];

	public function ref_tahun_anggaran()
	{
		return $this->belongsTo(RefTahunAnggaran::class, 'ID_TA_ANGGARAN');
	}

	public function mst_unit()
	{
		return $this->belongsTo(MstUnit::class, 'ID_UNIT');
	}

	public function ref_tan()
	{
		return $this->belongsTo(RefTan::class, 'ID_TAN');
	}

	public function mst_coa()
	{
		return $this->belongsTo(MstCoa::class, 'ID_MASTER_COA');
	}

	public function mst_kegiatan()
	{
		return $this->belongsTo(MstKegiatan::class, 'ID_KEGIATAN');
	}

	public function dtl_program_kerjas()
	{
		return $this->hasMany(DtlProgramKerja::class, 'ID_PROGRAM_KERJA');
	}

	public function fpd_anggarans()
	{
		return $this->hasMany(FpdAnggaran::class, 'ID_PROGRAM_KERJA');
	}

	public function tr_pms()
	{
		return $this->hasMany(TrPm::class, 'ID_PROGRAM_KERJA');
	}
}
