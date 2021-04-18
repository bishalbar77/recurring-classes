<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Classes;

class ClassesStudents extends Model
{
    
    protected $guarded = [];

    
    public function student()
    {
        return $this->belongsTo('App\User', 'student_id');
    }
    
    public function classDetails()
    {
        return $this->belongsTo('App\Classes', 'class_id');
    }
}
