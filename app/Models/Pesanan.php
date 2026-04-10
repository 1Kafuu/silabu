<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    protected $table = "pesanan";
    protected $primaryKey = "idpesanan";
    protected $fillable = ['iduser', 'nama', 'total', 'metode_bayar', 'status_bayar', 'snap_token'];

    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }
}
