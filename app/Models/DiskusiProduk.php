<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Pembeli;
use App\Models\Barang;

class DiskusiProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'diskusi_produk';
    protected $primaryKey = 'diskusi_id';

    public $timestamps = false;

    protected $fillable = [
        'pembeli_id',
        'barang_id',
        'pertanyaan',
        'jawaban',
        'tanggal_diskusi',
    ];

    protected $dates = [
        'tanggal_diskusi',
        'deleted_at'
    ];

    // ADDED: Cast tanggal_diskusi to datetime
    protected $casts = [
        'tanggal_diskusi' => 'datetime',
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // ADDED: Accessor for user (alias for pembeli)
    public function user()
    {
        return $this->pembeli();
    }

    // ADDED: Method to check if discussion has answer
    public function hasAnswer()
    {
        return !empty($this->jawaban);
    }

    // ADDED: Method to get formatted date
    public function getFormattedDateAttribute()
    {
        return $this->tanggal_diskusi ? $this->tanggal_diskusi->format('d M Y H:i') : '-';
    }

    // ADDED: Scope for answered discussions
    public function scopeAnswered($query)
    {
        return $query->whereNotNull('jawaban')->where('jawaban', '!=', '');
    }

    // ADDED: Scope for unanswered discussions
    public function scopeUnanswered($query)
    {
        return $query->where(function($q) {
            $q->whereNull('jawaban')->orWhere('jawaban', '');
        });
    }

    // ADDED: Scope for recent discussions
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('tanggal_diskusi', '>=', now()->subDays($days));
    }
}
