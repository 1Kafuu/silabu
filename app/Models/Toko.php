<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = "toko";
    protected $primaryKey = "idtoko";
    public $timestamps = false;
    protected $fillable = ['nama_toko', 'latitude', 'longtitude', 'accuracy'];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'idtoko', 'idtoko');
    }
}
