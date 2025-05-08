<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Penitip extends Model
{
    use HasFactory;

    protected $table = 'penitip';
    protected $primaryKey = 'penitip_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'point_donasi',
        'tanggal_registrasi',
        'no_ktp',
        'user_id',
        'badge',
        'periode',
        'saldo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
