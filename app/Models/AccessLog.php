<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccessLog
 * 
 * @property int $ID_ACCESS_LOG
 * @property Carbon|null $START_LOGIN
 * @property Carbon|null $END_LOGIN
 * @property string|null $USERNAME
 * @property string|null $ROLE
 * 
 * @property Collection|ActivityLog[] $activity_logs
 *
 * @package App\Models
 */
class AccessLog extends Model
{
	protected $table = 'access_log';
	protected $primaryKey = 'ID_ACCESS_LOG';
	public $timestamps = false;

	protected $casts = [
		'START_LOGIN' => 'datetime',
		'END_LOGIN' => 'datetime'
	];

	protected $fillable = [
		'START_LOGIN',
		'END_LOGIN',
		'USERNAME',
		'ROLE'
	];

	public function activity_logs()
	{
		return $this->hasMany(ActivityLog::class, 'ID_ACCESS_LOG');
	}
}
