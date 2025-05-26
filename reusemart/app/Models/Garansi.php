<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garansi extends Model
{
    use HasFactory;

    protected $table = 'garansi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'status',
        'tanggal_aktif',
        'tanggal_berakhir',
    ];
}
