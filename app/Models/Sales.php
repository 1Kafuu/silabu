<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = "sales";
    protected $primaryKey = "idsales";
    public $timestamps = false;
    protected $fillable = ['idtoko', 'latitude', 'longitude', 'accuracy', 'jarak', 'status', 'waktu'];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'idtoko', 'idtoko');
    }
}
