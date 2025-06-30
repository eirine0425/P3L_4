<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PickupSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'penitip_id',
        'pickup_method',
        'scheduled_date',
        'scheduled_time',
        'pickup_address',
        'contact_phone',
        'notes',
        'status',
        'total_items',
        'completed_at',
        'completed_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'penitip_id');
    }

    public function items()
    {
        return $this->hasMany(Barang::class, 'pickup_schedule_id');
    }

    public function logs()
    {
        return $this->hasMany(PickupLog::class, 'pickup_schedule_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Accessors
    public function getFormattedScheduledDateAttribute()
    {
        return $this->scheduled_date->format('d M Y');
    }

    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'confirmed':
                return 'bg-primary';
            case 'in_progress':
                return 'bg-warning';
            case 'completed':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'confirmed':
                return 'Terkonfirmasi';
            case 'in_progress':
                return 'Sedang Diproses';
            case 'completed':
                return 'Selesai';
            case 'cancelled':
                return 'Dibatalkan';
            default:
                return 'Tidak Diketahui';
        }
    }

    public function getPickupMethodTextAttribute()
    {
        switch ($this->pickup_method) {
            case 'self_pickup':
                return 'Ambil Sendiri';
            case 'courier_delivery':
                return 'Kirim via Kurir';
            default:
                return 'Tidak Diketahui';
        }
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', Carbon::today());
    }
}
