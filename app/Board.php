<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public function user() {
    	return $this->belongsTo("App\User");
    }

    public function pins()
    {
        return $this->hasMany('App\Pin');
    }
}
