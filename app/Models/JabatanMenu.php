<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JabatanMenu
 * 
 * @property int $ID_HAK_AKSES
 * @property int|null $ID_SI_ROLE_MENU
 * @property int|null $ID_JABATAN
 * 
 * @property MstSiMenu|null $mst_si_menu
 * @property RefJabatanStr|null $ref_jabatan_str
 *
 * @package App\Models
 */
class JabatanMenu extends Model
{
	protected $table = 'jabatan_menu';
	protected $primaryKey = 'ID_HAK_AKSES';
	public $timestamps = false;

	protected $casts = [
		'ID_SI_ROLE_MENU' => 'int',
		'ID_JABATAN' => 'int'
	];

	protected $fillable = [
		'ID_SI_ROLE_MENU',
		'ID_JABATAN'
	];

	public function mst_si_menu()
	{
		return $this->belongsTo(MstSiMenu::class, 'ID_SI_ROLE_MENU');
	}

	public function ref_jabatan_str()
	{
		return $this->belongsTo(RefJabatanStr::class, 'ID_JABATAN');
	}
}
