<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'pegawai_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nama_jabatan',
        'tanggal_bergabung',
        'nominal_komisi',
        'status_aktif',
        'nama',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
