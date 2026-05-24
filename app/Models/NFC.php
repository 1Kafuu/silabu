<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFC extends Model
{
    protected $table = 'nfc_cards';
    protected $primaryKey = 'id';
    protected $fillable = ['student_id', 'serial_number'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
