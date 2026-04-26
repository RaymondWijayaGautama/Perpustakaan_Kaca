<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefTahunAnggaran
 * 
 * @property int $ID_TA_ANGGARAN
 * @property bool|null $IS_CURRENT
 * @property string|null $DESKRIPSI_TAHUN_ANGGARAN
 * 
 * @property Collection|MstProgramKerja[] $mst_program_kerjas
 * @property Collection|RefTarif[] $ref_tarifs
 *
 * @package App\Models
 */
class RefTahunAnggaran extends Model
{
	protected $table = 'ref_tahun_anggaran';
	protected $primaryKey = 'ID_TA_ANGGARAN';
	public $timestamps = false;

	protected $casts = [
		'IS_CURRENT' => 'bool'
	];

	protected $fillable = [
		'IS_CURRENT',
		'DESKRIPSI_TAHUN_ANGGARAN'
	];

	public function mst_program_kerjas()
	{
		return $this->hasMany(MstProgramKerja::class, 'ID_TA_ANGGARAN');
	}

	public function ref_tarifs()
	{
		return $this->hasMany(RefTarif::class, 'ID_TA_ANGGARAN');
	}
}
