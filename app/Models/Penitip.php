<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'ktp_path',
    ];

    protected $appends = [
        'average_rating',
        'total_ratings',
        'star_display',
        'rating_distribution',
        'rating_badge_class',
        'rating_text',
    ];

    /**
     * Get all ratings for this consignor's items
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratings()
    {
        return Rating::whereHas('barang', function($query) {
            $query->where('penitip_id', $this->penitip_id);
        });
    }

    /**
     * Get average rating for this consignor
     */
    public function getAverageRatingAttribute()
    {
        $averageRating = $this->ratings()->avg('rating');
        return $averageRating ? round($averageRating, 1) : 0;
    }

    /**
     * Get total number of ratings for this consignor
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get star display for average rating
     */
    public function getStarDisplayAttribute()
    {
        $rating = $this->average_rating;
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        
        $stars = str_repeat('★', $fullStars);
        if ($hasHalfStar) {
            $stars .= '☆';
            $fullStars++;
        }
        $stars .= str_repeat('☆', 5 - $fullStars);
        
        return $stars;
    }

    /**
     * Get rating distribution for this consignor
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get rating badge class based on average rating
     */
    public function getRatingBadgeClassAttribute()
    {
        $rating = $this->average_rating;
        
        if ($rating >= 4.5) {
            return 'badge-success';
        } elseif ($rating >= 4.0) {
            return 'badge-primary';
        } elseif ($rating >= 3.0) {
            return 'badge-warning';
        } elseif ($rating >= 2.0) {
            return 'badge-danger';
        } else {
            return 'badge-secondary';
        }
    }

    /**
     * Get rating text description
     */
    public function getRatingTextAttribute()
    {
        $rating = $this->average_rating;
        
        if ($rating >= 4.5) {
            return 'Excellent';
        } elseif ($rating >= 4.0) {
            return 'Very Good';
        } elseif ($rating >= 3.0) {
            return 'Good';
        } elseif ($rating >= 2.0) {
            return 'Fair';
        } elseif ($rating > 0) {
            return 'Poor';
        } else {
            return 'No Ratings';
        }
    }
}
