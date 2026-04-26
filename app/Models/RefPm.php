<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefPm
 * 
 * @property int $ID_REF_PM
 * @property int|null $REF_ID_REF_PM
 * @property string|null $NAMA_PM
 * @property string|null $DESKRIPSI_PM
 * 
 * @property RefPm|null $ref_pm
 * @property Collection|RefPm[] $ref_pms
 * @property Collection|TrPm[] $tr_pms
 *
 * @package App\Models
 */
class RefPm extends Model
{
	protected $table = 'ref_pm';
	protected $primaryKey = 'ID_REF_PM';
	public $timestamps = false;

	protected $casts = [
		'REF_ID_REF_PM' => 'int'
	];

	protected $fillable = [
		'REF_ID_REF_PM',
		'NAMA_PM',
		'DESKRIPSI_PM'
	];

	public function ref_pm()
	{
		return $this->belongsTo(RefPm::class, 'REF_ID_REF_PM');
	}

	public function ref_pms()
	{
		return $this->hasMany(RefPm::class, 'REF_ID_REF_PM');
	}

	public function tr_pms()
	{
		return $this->hasMany(TrPm::class, 'ID_REF_PM');
	}
}
