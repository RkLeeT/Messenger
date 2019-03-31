<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function hasUsers()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

}
