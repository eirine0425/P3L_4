<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDonasi extends Model
{
    use HasFactory;

    protected $table = 'request_donasi';
    protected $primaryKey = 'request_id';
    
    protected $fillable = [
        'organisasi_id',
        'tanggal_request',
        'status_request',
        'deskripsi',
        'jumlah_barang_diminta'
    ];

    protected $casts = [
        'tanggal_request' => 'date'
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'organisasi_id');
    }

    public function donasi()
    {
        return $this->hasMany(Donasi::class, 'request_id', 'request_id');
    }
}
