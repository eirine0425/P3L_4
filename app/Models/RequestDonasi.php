<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDonasi extends Model
{
    protected $table = 'request_donasi'; // Nama tabel yang digunakan di database
    protected $primaryKey = 'request_id'; // Primary key yang digunakan di tabel
    public $timestamps = false; // Jika tabel tidak menggunakan kolom created_at dan updated_at

    protected $fillable = [
        'request_id',        // ID permintaan
        'organisasi_id',     // ID organisasi yang membuat permintaan
        'deskripsi',         // Deskripsi permintaan
        'tanggal_request',   // Tanggal permintaan dibuat
        'status_request',    // Status permintaan (misalnya: pending, approved, rejected)
    ];

    public function organisasi()
{
    return $this->belongsTo(Organisasi::class, 'organisasi_id');
}

}

