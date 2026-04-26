<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DtTrPkg
 * 
 * @property int $ID_DT_TR_PKG
 * @property int|null $ID_TR_PKG
 * @property int|null $ID_MST_PKG
 * @property string|null $NILAI_KOMPETENSI_PKG
 * @property bool|null $IS_VALID_PKG
 * 
 * @property TrPkg|null $tr_pkg
 * @property MstPkg|null $mst_pkg
 *
 * @package App\Models
 */
class DtTrPkg extends Model
{
	protected $table = 'dt_tr_pkg';
	protected $primaryKey = 'ID_DT_TR_PKG';
	public $timestamps = false;

	protected $casts = [
		'ID_TR_PKG' => 'int',
		'ID_MST_PKG' => 'int',
		'IS_VALID_PKG' => 'bool'
	];

	protected $fillable = [
		'ID_TR_PKG',
		'ID_MST_PKG',
		'NILAI_KOMPETENSI_PKG',
		'IS_VALID_PKG'
	];

	public function tr_pkg()
	{
		return $this->belongsTo(TrPkg::class, 'ID_TR_PKG');
	}

	public function mst_pkg()
	{
		return $this->belongsTo(MstPkg::class, 'ID_MST_PKG');
	}
}
