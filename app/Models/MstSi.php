<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstSi
 * 
 * @property int $ID_SI
 * @property string|null $NAMA_SI
 * @property string|null $DESKRIPSI_SI
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|MstSiMenu[] $mst_si_menus
 *
 * @package App\Models
 */
class MstSi extends Model
{
	protected $table = 'mst_si';
	protected $primaryKey = 'ID_SI';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_SI',
		'DESKRIPSI_SI',
		'IS_DELETE'
	];

	public function mst_si_menus()
	{
		return $this->hasMany(MstSiMenu::class, 'ID_SI');
	}
}
