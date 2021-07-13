<?php

namespace App\Observers;

use App\Models\Autosave;
use Illuminate\Support\Str;

class AutosaveObserver
{
    /**
     * Handle the Autosave "created" event.
     *
     * @param  \App\Models\Autosave  $autosave
     * @return void
     */
    public function created(Autosave $autosave)
    {
        $autosave->identifier = Str::uuid();

        $autosave->saveQuietly();
    }

    /**
     * Handle the Autosave "updated" event.
     *
     * @param  \App\Models\Autosave  $autosave
     * @return void
     */
    public function updated(Autosave $autosave)
    {
        //
    }

    /**
     * Handle the Autosave "deleted" event.
     *
     * @param  \App\Models\Autosave  $autosave
     * @return void
     */
    public function deleted(Autosave $autosave)
    {
        //
    }

    /**
     * Handle the Autosave "restored" event.
     *
     * @param  \App\Models\Autosave  $autosave
     * @return void
     */
    public function restored(Autosave $autosave)
    {
        //
    }

    /**
     * Handle the Autosave "force deleted" event.
     *
     * @param  \App\Models\Autosave  $autosave
     * @return void
     */
    public function forceDeleted(Autosave $autosave)
    {
        //
    }
}
