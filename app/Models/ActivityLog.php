<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityLog
 * 
 * @property int $ID_ACTIVITY_LOG
 * @property int|null $ID_ACCESS_LOG
 * @property Carbon|null $EVENT_TIME
 * @property string|null $ACTOR_USERNAME
 * @property string|null $ACTOR_ROLE
 * @property string|null $ACTIVITY_NAME
 * @property string|null $RELATED_DATA
 * @property string|null $ACTIVITY_DESCRIPTION
 * 
 * @property AccessLog|null $access_log
 *
 * @package App\Models
 */
class ActivityLog extends Model
{
	protected $table = 'activity_log';
	protected $primaryKey = 'ID_ACTIVITY_LOG';
	public $timestamps = false;

	protected $casts = [
		'ID_ACCESS_LOG' => 'int',
		'EVENT_TIME' => 'datetime'
	];

	protected $fillable = [
		'ID_ACCESS_LOG',
		'EVENT_TIME',
		'ACTOR_USERNAME',
		'ACTOR_ROLE',
		'ACTIVITY_NAME',
		'RELATED_DATA',
		'ACTIVITY_DESCRIPTION'
	];

	public function access_log()
	{
		return $this->belongsTo(AccessLog::class, 'ID_ACCESS_LOG');
	}
}
