<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'poli';
    protected $fillable = [
        'name',
        'description',
    ];
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
