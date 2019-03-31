<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupsMessages extends Model
{
    protected $guarded = [];

    protected $table = 'groups_messages';

    public function fromContact()
    {
    	return $this->hasOne(User::class, 'id', 'user_id');
    }
}
