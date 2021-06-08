<?php

namespace App\Observers;

use App\Events\MeetingChangeEvent;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Room;
use App\Notifications\NewMeeting;

class MeetingObserver
{
    /**
     * Handle the Meeting "created" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function created(Meeting $meeting)
    {
        broadcast(new MeetingChangeEvent($meeting));

        foreach ($meeting->classroom->students as $student) {
            $student->notify(new NewMeeting($meeting));
        }

        $studentUserIds = $meeting->classroom->students->pluck("id");

        $room = new Room();
        $room->name = "Diskusi Kelas";
        $room->identifier = "meeting.general." . $meeting->id;
        $meeting->rooms()->save($room);

        $room
            ->users()
            ->attach(
                array_merge([$meeting->teacher->id], $studentUserIds->toArray())
            );

        $absents = $meeting->teacher
            ->studentabsents()
            ->whereDate("finish_at", ">", now())
            ->get();

        $absentsMap = [];

        foreach ($absents as $value) {
            $absentsMap[$value->user_id] = $value;
        }

        foreach ($studentUserIds as $id) {
            if (array_key_exists($id, $absentsMap)) {
                Attendance::firstOrCreate([
                    "user_id" => $id,
                    "attendable_id" => $meeting->id,
                    "attendable_type" => Meeting::class,
                    "attended" => false,
                    "reason" => $absentsMap[$id]->reason,
                ]);
            } else {
                Attendance::firstOrCreate([
                    "user_id" => $id,
                    "attendable_id" => $meeting->id,
                    "attendable_type" => Meeting::class,
                ]);
            }
        }
    }

    /**
     * Handle the Meeting "updated" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function updated(Meeting $meeting)
    {
        broadcast(new MeetingChangeEvent($meeting));
    }

    /**
     * Handle the Meeting "deleted" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function deleted(Meeting $meeting)
    {
        //
    }

    /**
     * Handle the Meeting "restored" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function restored(Meeting $meeting)
    {
        //
    }

    /**
     * Handle the Meeting "force deleted" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function forceDeleted(Meeting $meeting)
    {
        //
    }
}
