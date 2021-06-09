<?php

namespace App\Observers;

use App\Models\Agenda;
use Illuminate\Support\Str;

class AgendaObserver
{
    /**
     * Handle the Agenda "created" event.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return void
     */
    public function created(Agenda $agenda)
    {
        $agenda->uuid = Str::uuid();
        $agenda->saveQuietly();
    }

    /**
     * Handle the Agenda "updated" event.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return void
     */
    public function updated(Agenda $agenda)
    {
        //
    }

    /**
     * Handle the Agenda "deleted" event.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return void
     */
    public function deleted(Agenda $agenda)
    {
        //
    }

    /**
     * Handle the Agenda "restored" event.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return void
     */
    public function restored(Agenda $agenda)
    {
        //
    }

    /**
     * Handle the Agenda "force deleted" event.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return void
     */
    public function forceDeleted(Agenda $agenda)
    {
        //
    }
}
