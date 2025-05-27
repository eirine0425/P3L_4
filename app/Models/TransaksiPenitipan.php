<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Penitip;
use App\Models\Barang;
use Carbon\Carbon;

class TransaksiPenitipan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penitipan';
    protected $primaryKey = 'transaksi_penitipan_id';
    public $timestamps = false;

    protected $fillable = [
        'penitip_id',
        'barang_id',
        'batas_penitipan',
        'tanggal_penitipan',
        'metode_penitipan',
        'status_perpanjangan',
        'status_penitipan',
    ];

    protected $appends = ['durasi_penitipan', 'sisa_hari', 'is_expired', 'status_durasi'];

    protected $casts = [
        'tanggal_penitipan' => 'datetime',
        'batas_penitipan' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaksiPenitipan) {
            if ($transaksiPenitipan->tanggal_penitipan && !$transaksiPenitipan->batas_penitipan) {
                $transaksiPenitipan->batas_penitipan = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->addDays(30);
            }
        });
        
        static::updating(function ($transaksiPenitipan) {
            if ($transaksiPenitipan->isDirty('tanggal_penitipan') && $transaksiPenitipan->tanggal_penitipan) {
                $transaksiPenitipan->batas_penitipan = Carbon::parse($transaksiPenitipan->tanggal_penitipan)->addDays(30);
            }
        });
    }

    /**
     * Calculate consignment duration in days (always 30 days)
     */
    public function getDurasiPenitipanAttribute()
    {
        return 30;
    }

    /**
     * Get remaining days until consignment deadline
     */
    public function getSisaHariAttribute()
    {
        if (!$this->batas_penitipan) {
            return null;
        }
        
        $now = Carbon::now();
        $batas = Carbon::parse($this->batas_penitipan);
        
        return $now->diffInDays($batas, false); // false means it can be negative
    }

    /**
     * Check if consignment is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->batas_penitipan) {
            return false;
        }
        
        return Carbon::now()->isAfter(Carbon::parse($this->batas_penitipan));
    }

    /**
     * Get status based on remaining days
     */
    public function getStatusDurasiAttribute()
    {
        $sisaHari = $this->sisa_hari;
        
        if ($sisaHari === null) {
            return 'unknown';
        }
        
        if ($sisaHari < 0) {
            return 'expired';
        } elseif ($sisaHari <= 7) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
