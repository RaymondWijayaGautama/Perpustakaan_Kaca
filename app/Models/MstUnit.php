<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstUnit
 * 
 * @property int $ID_UNIT
 * @property string $NIP_KARYAWAN
 * @property string|null $KODE_UNIT
 * @property string|null $NAMA_UNIT
 * @property bool|null $IS_DELETE
 * 
 * @property MstKaryawan $mst_karyawan
 * @property Collection|MstKaryawan[] $mst_karyawans
 * @property Collection|MstProgramKerja[] $mst_program_kerjas
 *
 * @package App\Models
 */
class MstUnit extends Model
{
	protected $table = 'mst_unit';
	protected $primaryKey = 'ID_UNIT';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NIP_KARYAWAN',
		'KODE_UNIT',
		'NAMA_UNIT',
		'IS_DELETE'
	];

	public function mst_karyawan()
	{
		return $this->belongsTo(MstKaryawan::class, 'NIP_KARYAWAN');
	}

	public function mst_karyawans()
	{
		return $this->hasMany(MstKaryawan::class, 'ID_UNIT');
	}

	public function mst_program_kerjas()
	{
		return $this->hasMany(MstProgramKerja::class, 'ID_UNIT');
	}
}
