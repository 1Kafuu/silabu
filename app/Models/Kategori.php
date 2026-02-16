<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = "kategori";
    protected $primaryKey = "idkategori";
    protected $fillable = ['nama_kategori', 'deleted_at'];
    
    public function book()
    {
        return $this->hasMany(Buku::class,'idkategori','idkategori');
    }
}
