<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'role';
    protected $primaryKey = 'idrole';
    protected $fillable = ['nama_role'];

    public $timestamps = false;

    public function role_user() {
        return $this->hasMany(RoleUser::class, 'idrole', 'idrole');        
    }

    public function user() {
        return $this->belongsToMany(User::class, 'role_user', 'idrole', 'iduser');
    }
}
