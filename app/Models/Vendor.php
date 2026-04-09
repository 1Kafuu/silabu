<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'idvendor';
    protected $table = 'vendor';
    protected $fillable = ['nama_vendor', 'iduser'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }

    public function menu() {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }
}
