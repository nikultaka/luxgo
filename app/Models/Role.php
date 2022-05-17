<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Role extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'roles';
    protected $fillable = [
        'name','status','created_at','updated_at'
    ];

}
