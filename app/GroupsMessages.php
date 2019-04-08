<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupsMessages extends Model
{
    protected $guarded = [];

    protected $table = 'groups_messages';

    public function fromContact()
    {
    	return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function fromGroup()
    {
    	return $this->belongsTo(Group::class, 'id', 'group_id');
    }
}
