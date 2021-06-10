<?php

namespace App\Observers;

use App\Events\MeetingChangeEvent;
use App\Models\Agenda;
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
        $meeting->user = $meeting->user;

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
                array_merge([$meeting->user->id], $studentUserIds->toArray())
            );

        $absents = $meeting->user
            ->studentabsents()
            ->whereDate("finish_at", ">", now())
            ->get();

        $absentsMap = [];

        foreach ($absents as $value) {
            $absentsMap[$value->user_id] = $value;
        }

        $agenda = new Agenda();

        $agenda->agendaable_id = $meeting->id;
        $agenda->agendaable_type = Meeting::class;
        $agenda->user_id = $meeting->user_id;
        $agenda->name = "Absensi " . $meeting->name;
        $agenda->school_id = $meeting->classroom->school_id;
        $agenda->save();

        foreach ($studentUserIds as $id) {
            if (array_key_exists($id, $absentsMap)) {
                Attendance::firstOrCreate([
                    "school_id" => $meeting->classroom->school_id,
                    "user_id" => $id,
                    "attended" => false,
                    "reason" => $absentsMap[$id]->reason,
                    "agenda_id" => $agenda->id,
                ]);
            } else {
                Attendance::firstOrCreate([
                    "school_id" => $meeting->classroom->school_id,
                    "user_id" => $id,
                    "agenda_id" => $agenda->id,
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
        $meeting->user = $meeting->user;
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
