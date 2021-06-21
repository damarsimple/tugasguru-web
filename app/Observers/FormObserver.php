<?php

namespace App\Observers;

use App\Models\Form;
use App\Models\School;
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
                $this->handleFinished($form);
                break;
            case Form::PROCESSED:
                $form->user->notify(new FormProcessed($form));
                break;
            case Form::REJECTED:
                $form->user->notify(new FormRejected($form));
                break;
        }
    }

    public function handleFinished(Form $form)
    {

        // business logic

        switch ($form->type) {
            case Form::REQUEST_COUNSELOR:
                $user = $form->user;
                $user->is_counselor = true;
                $user->save();
                break;
            case Form::REQUEST_HOMEROOM:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_homeroom' => true]);
                $user->save();
                break;
            case Form::REQUEST_HEADMASTER:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_headmaster' => true]);
                $user->save();
                break;
            case Form::REQUEST_ADMIN_SCHOOL:
                $user = $form->user;
                $school = $form->school;
                $school->teachers()->updateExistingPivot($user, ['is_administrator' => true]);
                break;
            case Form::REQUEST_TUTOR:
                $user = $form->user;
                $user->is_bimbel = true;
                $user->save();
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
