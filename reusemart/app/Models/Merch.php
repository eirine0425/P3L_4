<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merch extends Model
{
    use HasFactory;

    protected $table = 'merch';
    protected $primaryKey = 'merch_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jumlah_poin',
        'stock_merch',
    ];
}
