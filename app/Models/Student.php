<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'NIM', 'fakultas', 'prodi'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function nfc()
    {
        return $this->hasOne(NFC::class, 'student_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }
}
