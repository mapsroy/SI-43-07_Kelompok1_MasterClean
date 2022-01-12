<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    //use HasFactory;
    public function layanan()
    {
        return $this->belongsTo('App\Layanan', 'Layanan_id', 'id');
    }

    public function pesanan()
    {
        return $this->belongsTo('App\Pesanan', 'pesanan_id', 'id');
    }
}
