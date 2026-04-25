<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJabatanStr
 * 
 * @property int $ID_JABATAN
 * @property string|null $DESKRIPSI_JABATAN
 * @property bool|null $IS_VALID_JABATAN
 * 
 * @property Collection|JabatanMenu[] $jabatan_menus
 * @property Collection|TrJabatan[] $tr_jabatans
 *
 * @package App\Models
 */
class RefJabatanStr extends Model
{
	protected $table = 'ref_jabatan_str';
	protected $primaryKey = 'ID_JABATAN';
	public $timestamps = false;

	protected $casts = [
		'IS_VALID_JABATAN' => 'bool'
	];

	protected $fillable = [
		'DESKRIPSI_JABATAN',
		'IS_VALID_JABATAN'
	];

	public function jabatan_menus()
	{
		return $this->hasMany(JabatanMenu::class, 'ID_JABATAN');
	}

	public function tr_jabatans()
	{
		return $this->hasMany(TrJabatan::class, 'ID_JABATAN');
	}
}
