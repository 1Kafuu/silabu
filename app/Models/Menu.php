<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'menu';
    protected $primaryKey = 'idmenu';
    protected $fillable = ['nama_menu', 'harga', 'path_gambar', 'idvendor'];
    public $timestamps = false;
    
    public function vendor(){
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

}
