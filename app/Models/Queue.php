<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = 'queues';

    protected $fillable = [
        'poli_id',
        'queue_number',
        'customer_name',
        'queue_order',
        'status',
        'called_at',
        'finished_at',
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }
}
