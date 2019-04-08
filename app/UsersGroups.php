<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UsersGroups extends Pivot
{
    protected $guarded = [];

    protected $table = 'group_user';

    public function ofContact()
    {
    	return $this->belongsToMany(User::class, 'id', 'user_id');
    }

    public function ofGroup()
    {
    	return $this->belongsToMany(Group::class, 'group_id', 'id');
    }
}
