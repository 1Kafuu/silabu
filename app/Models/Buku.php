<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = "buku";
    protected $primaryKey = "idbuku";
    protected $fillable = ['kode', 'judul', 'pengarang', 'idkategori', 'deleted_at'];

    public function category(){
        return $this->belongsTo(Kategori::class,"idkategori", "idkategori");
    }
}
