<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Alamat;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = false;

    protected $fillable = [
        'pembeli_id',
        'cs_id',
        'alamat_id',
        'tanggal_pelunasan',
        'point_digunakan',
        'point_diperoleh',
        'bukti_pembayaran',
        'metode_pengiriman',
        'tanggal_pesan',
        'subtotal',
        'total_harga',
        'status_transaksi',
        'batas_pembayaran',
        'batas_pembayaran',
    ];

    protected $dates = [
        'tanggal_pesan',
        'tanggal_pelunasan',
        'batas_pembayaran',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }

    public function customerService()
    {
        return $this->belongsTo(Pegawai::class, 'cs_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'transaksi_id');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    /**
     * Check if transaction is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->batas_pembayaran) {
            return false;
        }
        
        return now()->gt($this->batas_pembayaran);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->batas_pembayaran || $this->is_expired) {
            return 0;
        }
        
        return now()->diffInSeconds($this->batas_pembayaran);
    }

    /**
     * Get formatted remaining time
     */
    public function getFormattedRemainingTimeAttribute()
    {
        $seconds = $this->remaining_time;
        
        if ($seconds <= 0) {
            return '00:00';
        }
        
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
