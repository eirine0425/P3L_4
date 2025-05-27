<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    protected $table = 'foto_barang';

    protected $fillable = ['barang_id', 'path'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
