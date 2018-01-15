<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'name', 'student_id', 'password', 'phone', 'grade', 'created_at', 'updated_at'];

}
