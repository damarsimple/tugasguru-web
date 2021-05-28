<?php

use App\Models\Meeting;
use App\Models\PrivateRoom;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private.{id}', function ($user, $id) {

    if ($user->id == $id) {
        return $user;
    }

    return false;
});

Broadcast::channel('transaction.{id}', function ($user, $id) {

    $transaction = Transaction::findOrFail($id);

    if ($user->id == $transaction->user_id) {
        return $user;
    }

    return false;
});

Broadcast::channel('private_message.{id}', function ($user, $id) {

    $privateroom = PrivateRoom::findOrFail($id);
    if (in_array($user->id, [$privateroom->first_id, $privateroom->second_id])) {
        return $user;
    }

    return false;
});


Broadcast::channel('meeting.{id}', function ($user, $id) {

    $meeting = Meeting::findOrFail($id);

    if (in_array(
        $user->id,
        array_merge(
            [
                $meeting->teacher_id
            ],
            $meeting->classroom->students->pluck('id')->toArray()
        )
    )) {
        return $user;
    }

    return false;
});


Broadcast::channel('room.{id}', function ($user, $id) {

    $room = Room::findOrFail($id);

    if (in_array($user->id, $room->users->pluck('id')->toArray())) {
        return $user;
    }

    return false;
});
