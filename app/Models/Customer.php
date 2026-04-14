<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'customer';
    protected $primaryKey = 'idcustomer';
    protected $fillable = ['nama', 'alamat', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'kodepos', 'foto_blob', 'foto_path' ];
}
