<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MstSiMenu
 * 
 * @property int $ID_SI_ROLE_MENU
 * @property int|null $ID_SI
 * @property string|null $NAMA_MENU
 * @property string|null $DESKRIPSI_MENU
 * @property bool|null $IS_DELETE
 * 
 * @property MstSi|null $mst_si
 * @property Collection|JabatanMenu[] $jabatan_menus
 *
 * @package App\Models
 */
class MstSiMenu extends Model
{
	protected $table = 'mst_si_menu';
	protected $primaryKey = 'ID_SI_ROLE_MENU';
	public $timestamps = false;

	protected $casts = [
		'ID_SI' => 'int',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_SI',
		'NAMA_MENU',
		'DESKRIPSI_MENU',
		'IS_DELETE'
	];

	public function mst_si()
	{
		return $this->belongsTo(MstSi::class, 'ID_SI');
	}

	public function jabatan_menus()
	{
		return $this->hasMany(JabatanMenu::class, 'ID_SI_ROLE_MENU');
	}
}
