<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'password',
        'no_hp',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed', // jika Laravel >= 10
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', ownerKey: 'role_id');
    }
}


// <?php

// namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Laravel\Passport\HasApiTokens; // ✅ Tambahkan ini
// use App\Models\Pembeli;

// class User extends Authenticatable
// {
//     use HasApiTokens, HasFactory, Notifiable; // ✅ Tambahkan HasApiTokens di sini

//     protected $table = 'users';
// <<<<<<< HEAD
//     protected $primaryKey = 'user_id';
// =======
//     protected $primaryKey = 'id';
// >>>>>>> f7f4dded849a52ba73ea8bc6302de8a52446edea
//     public $timestamps = false;

//     protected $fillable = [
//         'name',
//         'email',
//         'dob',
//         'password',
//         'phone_number',
//         'role_id',
//     ];

//     protected $hidden = [
//         'password',
//     ];

//     protected $casts = [
//         'password' => 'hashed', 
//     ];

//     public function role(): BelongsTo
//     {
//         return $this->belongsTo(Role::class, 'role_id', ownerKey: 'role_id');
//     }
//     public function pembeli()
//     {
//         return $this->hasOne(Pembeli::class, 'user_id','id');
//     }
// }