<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrPemusnahanBuku
 * 
 * @property int $ID_PEMUSNAHAN_BUKU
 * @property int|null $ID_CP_KOLEKSI
 * @property string|null $KET_PEMUSNAHAN_BUKU
 * @property Carbon|null $TGL_PEMUSNAHAN_BUKU
 * @property bool|null $IS_DELETE
 * 
 * @property CpKoleksi|null $cp_koleksi
 *
 * @package App\Models
 */
class TrPemusnahanBuku extends Model
{
	protected $table = 'tr_pemusnahan_buku';
	protected $primaryKey = 'ID_PEMUSNAHAN_BUKU';
	public $timestamps = false;

	protected $casts = [
		'ID_CP_KOLEKSI' => 'int',
		'TGL_PEMUSNAHAN_BUKU' => 'datetime',
		'IS_DELETE' => 'bool'
	];

	protected $fillable = [
		'ID_CP_KOLEKSI',
		'KET_PEMUSNAHAN_BUKU',
		'TGL_PEMUSNAHAN_BUKU',
		'IS_DELETE'
	];

	public function cp_koleksi()
	{
		return $this->belongsTo(CpKoleksi::class, 'ID_CP_KOLEKSI');
	}
}
