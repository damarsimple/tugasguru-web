<?php

namespace App\Observers;

use App\Models\Assigment;
use App\Notifications\NewAssigment;

class AssigmentObserver
{
    /**
     * Handle the Assigment "created" event.
     *
     * @param  \App\Models\Assigment  $assigment
     * @return void
     */
    public function created(Assigment $assigment)
    {
        $users = $assigment->classroom->students;

        foreach ($users as $user) {
            $user->notify(new NewAssigment($assigment));
        }
    }

    /**
     * Handle the Assigment "updated" event.
     *
     * @param  \App\Models\Assigment  $assigment
     * @return void
     */
    public function updated(Assigment $assigment)
    {
        //
    }

    /**
     * Handle the Assigment "deleted" event.
     *
     * @param  \App\Models\Assigment  $assigment
     * @return void
     */
    public function deleted(Assigment $assigment)
    {
        //
    }

    /**
     * Handle the Assigment "restored" event.
     *
     * @param  \App\Models\Assigment  $assigment
     * @return void
     */
    public function restored(Assigment $assigment)
    {
        //
    }

    /**
     * Handle the Assigment "force deleted" event.
     *
     * @param  \App\Models\Assigment  $assigment
     * @return void
     */
    public function forceDeleted(Assigment $assigment)
    {
        //
    }
}
