<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Passport\HasApiTokens; // ✅ Tambahkan ini
use App\Models\Pembeli;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅ Tambahkan HasApiTokens di sini

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'dob',
        'password',
        'phone_number',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed', 
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', ownerKey: 'role_id');
    }
    public function pembeli()
    {
        return $this->hasOne(Pembeli::class, 'user_id','id');
    }
}