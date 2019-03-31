<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('messages.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('typing.{id}', function ($user, $id) {
    return auth()->check();
});


Broadcast::channel('privateStatus', function ($user) {
    if (auth()->check()) {
    	return $user;
    }
});


Broadcast::channel('group.{id}', function ($user, $id) {
    return (int) $user->inGroup()->where('id', id) === (int) $id;
});