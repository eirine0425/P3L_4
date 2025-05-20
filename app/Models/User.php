<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;  // Tambahkan ini
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;  // Tambahkan HasApiTokens di sini

    protected $table = 'users';
    protected $primaryKey = 'id'; // Sesuai migration
    public $timestamps = true;    // Sesuai migration

    protected $fillable = [
        'name',
        'email',
        'dob',
        'phone_number',
        'password',
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
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
