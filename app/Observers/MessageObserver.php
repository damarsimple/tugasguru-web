<?php

namespace App\Observers;

use App\Events\NewMessageEvent;
use App\Events\NewPrivateMessageEvent;
use App\Events\DeletePrivateMessageEvent;
use App\Events\DeleteMessageEvent;
use App\Models\Message;
use App\Models\PrivateRoom;
use App\Models\Room;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function created(Message $message)
    {
        if ($message->messageable_type == "App\Models\PrivateRoom") {
            broadcast(new NewPrivateMessageEvent($message));
        }

        if ($message->messageable_type == "App\Models\Room") {
            broadcast(new NewMessageEvent($message));
        }
    }

    /**
     * Handle the Message "updated" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function updated(Message $message)
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function deleted(Message $message)
    {
        if ($message->messageable_type == "App\Models\PrivateRoom") {
            broadcast(new DeletePrivateMessageEvent($message));
        }

        if ($message->messageable_type == "App\Models\Room") {
            broadcast(new DeleteMessageEvent($message));
        }
    }

    /**
     * Handle the Message "restored" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function restored(Message $message)
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function forceDeleted(Message $message)
    {
        if ($message->messageable_type == "App\Models\PrivateRoom") {
            broadcast(new DeletePrivateMessageEvent($message));
        }

        if ($message->messageable_type == "App\Models\Room") {
            broadcast(new DeleteMessageEvent($message));
        }
    }
}
