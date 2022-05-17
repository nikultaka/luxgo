<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;
    
    protected $guard = 'admin';

	protected $table = 'admins';
    public $timestamps = false;
	protected $fillable = [
        'id','name','email','role_id','password','created_at','updated_at','status','is_admin'
    ];
}
