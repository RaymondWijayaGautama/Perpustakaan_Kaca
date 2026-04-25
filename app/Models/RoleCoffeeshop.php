<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleCoffeeshop
 * 
 * @property int $ID_ROLE_COFFEESHOP
 * @property string|null $NAMA_ROLE_COFFEESHOP
 * @property bool|null $IS_DELETE
 * 
 * @property Collection|JadwalCoffeeshop[] $jadwal_coffeeshops
 *
 * @package App\Models
 */
class RoleCoffeeshop extends Model
{
	protected $table = 'role_coffeeshop';
	protected $primaryKey = 'ID_ROLE_COFFEESHOP';
	public $timestamps = false;

	protected $casts = [
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'NAMA_ROLE_COFFEESHOP',
		'IS_DELETE'
	];

	public function jadwal_coffeeshops()
	{
		return $this->hasMany(JadwalCoffeeshop::class, 'ID_ROLE_COFFEESHOP');
	}
}
