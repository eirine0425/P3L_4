<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat';
    protected $primaryKey = 'alamat_id';

    protected $fillable = [
        'pembeli_id',
        'nama_penerima',
        'alamat',
        'kode_pos',
        'kota',
        'provinsi', 
        'no_telepon',
        'status_default',
    ];

    // ADDED: Cast untuk memastikan status_default selalu string
    protected $casts = [
        'status_default' => 'string',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id', 'pembeli_id');
    }

    // ADDED: Scope untuk alamat default
    public function scopeDefault($query)
    {
        return $query->where('status_default', 'Y');
    }

    // ADDED: Scope untuk alamat berdasarkan pembeli
    public function scopeForPembeli($query, $pembeliId)
    {
        return $query->where('pembeli_id', $pembeliId);
    }

    public function getTable()
    {
        return 'alamat';
    }
}
