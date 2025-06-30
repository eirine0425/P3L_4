<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';
    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'pembeli_id',
        'barang_id',
        'transaksi_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Validation rules
    public static $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'nullable|string|max:1000',
    ];

    /**
     * Relationship with Pembeli
     */
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'pembeli_id', 'pembeli_id');
    }

    /**
     * Relationship with Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    /**
     * Relationship with Transaksi
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    /**
     * Get star display for rating
     */
    public function getStarDisplayAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    /**
     * Get rating text
     */
    public function getRatingTextAttribute()
    {
        $texts = [
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik'
        ];
        
        return $texts[$this->rating] ?? 'Tidak Diketahui';
    }

    /**
     * Scope for specific rating value
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for ratings above certain value
     */
    public function scopeAboveRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope for recent ratings
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
