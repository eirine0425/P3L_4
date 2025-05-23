<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Organisasi extends Model
{
    use HasFactory;

    protected $table = 'organisasi';
    protected $primaryKey = 'organisasi_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_organisasi',
        'user_id',
        'alamat',
        'deskripsi',
        'dokumen_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
