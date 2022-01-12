<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    //use HasFactory;
    public function pesanan_detail()
    {
        return $this->hasMany('App\PesananDetail', 'Layanan_id', 'id');
    }
}
