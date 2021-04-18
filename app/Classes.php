<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Classes extends Model
{
    protected $guarded = [];

    public function teacher()
    {
        return $this->belongsTo('App\User', 'teacher_id');
    }
}
