<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RefJenisPembayaran
 * 
 * @property int $ID_JENIS_PEMBAYARAN
 * @property string|null $DESKRIPSI_JENIS_PEMBAYARAN
 * 
 * @property Collection|TagihanSiswa[] $tagihan_siswas
 * @property Collection|TrPembayaran[] $tr_pembayarans
 *
 * @package App\Models
 */
class RefJenisPembayaran extends Model
{
	protected $table = 'ref_jenis_pembayaran';
	protected $primaryKey = 'ID_JENIS_PEMBAYARAN';
	public $timestamps = false;

	protected $fillable = [
		'DESKRIPSI_JENIS_PEMBAYARAN'
	];

	public function tagihan_siswas()
	{
		return $this->hasMany(TagihanSiswa::class, 'ID_JENIS_PEMBAYARAN');
	}

	public function tr_pembayarans()
	{
		return $this->hasMany(TrPembayaran::class, 'REF_ID_JENIS_PEMBAYARAN');
	}
}
