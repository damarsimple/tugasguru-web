<?php

namespace App\Observers;

use App\Models\Form;
use App\Notifications\FormApproved;
use App\Notifications\FormProcessed;
use App\Notifications\FormRejected;

class FormObserver
{
    /**
     * Handle the Form "created" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function created(Form $form)
    {
        //
    }

    /**
     * Handle the Form "updated" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function updated(Form $form)
    {
        switch ($form->status) {
            case Form::FINISHED:
                $form->user->notify(new FormApproved($form));
                break;
            case Form::PROCESSED:
                $form->user->notify(new FormProcessed($form));
                break;
            case Form::REJECTED:
                $form->user->notify(new FormRejected($form));
                break;
        }
    }

    /**
     * Handle the Form "deleted" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function deleted(Form $form)
    {
        //
    }

    /**
     * Handle the Form "restored" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function restored(Form $form)
    {
        //
    }

    /**
     * Handle the Form "force deleted" event.
     *
     * @param  \App\Models\Form  $form
     * @return void
     */
    public function forceDeleted(Form $form)
    {
        //
    }
}
