<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function hasMessages()
    {
    	return $this->hasMany(GroupsMessages::class, 'group_id', 'id');
    }


}
