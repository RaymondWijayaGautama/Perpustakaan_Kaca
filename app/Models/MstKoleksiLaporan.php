<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstKoleksiLaporan
 * 
 * @property int $ID_MST_LAPORAN
 * @property int|null $ID_PKL_SISWA
 * @property bool|null $IS_DELETE
 * 
 * @property PklSiswa|null $pkl_siswa
 * @property Collection|CpKoleksi[] $cp_koleksis
 *
 * @package App\Models
 */
class MstKoleksiLaporan extends Model
{
	protected $table = 'mst_koleksi_laporan';
	protected $primaryKey = 'ID_MST_LAPORAN';
	public $timestamps = false;

	protected $casts = [
		'ID_PKL_SISWA' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_PKL_SISWA',
		'IS_DELETE'
	];

	public function pkl_siswa()
	{
		return $this->belongsTo(PklSiswa::class, 'ID_PKL_SISWA');
	}

	public function cp_koleksis()
	{
		return $this->hasMany(CpKoleksi::class, 'ID_MST_LAPORAN');
	}
}
