<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\Examsession;

class ExamsessionObserver
{
    /**
     * Handle the Examsession "created" event.
     *
     * @param  \App\Models\Examsession  $examsession
     * @return void
     */
    public function created(Examsession $examsession)
    {
        // $exam = $examsession->exam;

        // $clasrooms = $examsession->classrooms()->with('students')->get();

        // $events = [];

        // foreach ($clasrooms as $clasroom) {
        //     foreach ($clasroom->students as $student) {
        //         $event = new Event();
        //         $event->name = "Ulangan " . $exam->name;
        //         $event->begin_at = $examsession->open_at;
        //         $event->eventable_id = $examsession->id;
        //         $event->eventable_type = Examsession::class;
        //         $event->user_id = $student->user_id;

        //         $events[] = $event;
        //     }
        // }

        // Event::insert($events);
    }

    /**
     * Handle the Examsession "updated" event.
     *
     * @param  \App\Models\Examsession  $examsession
     * @return void
     */
    public function updated(Examsession $examsession)
    {
        //
    }

    /**
     * Handle the Examsession "deleted" event.
     *
     * @param  \App\Models\Examsession  $examsession
     * @return void
     */
    public function deleted(Examsession $examsession)
    {
        //
    }

    /**
     * Handle the Examsession "restored" event.
     *
     * @param  \App\Models\Examsession  $examsession
     * @return void
     */
    public function restored(Examsession $examsession)
    {
        //
    }

    /**
     * Handle the Examsession "force deleted" event.
     *
     * @param  \App\Models\Examsession  $examsession
     * @return void
     */
    public function forceDeleted(Examsession $examsession)
    {
        //
    }
}
