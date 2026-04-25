<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstPkg
 * 
 * @property int $ID_MST_PKG
 * @property string|null $NAMA_KOMPETENSI_PKG
 * @property string|null $JENIS_KOMPETENSI_PKG
 * @property float|null $BOBOT_KOMPETENSI_PKG
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|DtTrPkg[] $dt_tr_pkgs
 *
 * @package App\Models
 */
class MstPkg extends Model
{
	protected $table = 'mst_pkg';
	protected $primaryKey = 'ID_MST_PKG';
	public $timestamps = false;

	protected $casts = [
		'BOBOT_KOMPETENSI_PKG' => 'float',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_KOMPETENSI_PKG',
		'JENIS_KOMPETENSI_PKG',
		'BOBOT_KOMPETENSI_PKG',
		'IS_DELETE'
	];

	public function dt_tr_pkgs()
	{
		return $this->hasMany(DtTrPkg::class, 'ID_MST_PKG');
	}
}
