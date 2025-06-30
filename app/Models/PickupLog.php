<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'pickup_schedule_id',
        'action',
        'performed_by',
        'notes',
        'created_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function pickupSchedule()
    {
        return $this->belongsTo(PickupSchedule::class, 'pickup_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getActionTextAttribute()
    {
        switch ($this->action) {
            case 'confirmed':
                return 'Dikonfirmasi';
            case 'picked_up':
                return 'Diambil';
            case 'cancelled':
                return 'Dibatalkan';
            default:
                return ucfirst($this->action);
        }
    }
}
