<?php

namespace App\Observers;

use App\Models\Form;
use App\Models\StudentPpdb;

class StudentPpdbObserver
{
    /**
     * Handle the StudentPpdb "created" event.
     *
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return void
     */
    public function created(StudentPpdb $studentPpdb)
    {
        $this->handleCreateForm($studentPpdb);
    }

    private function handleCreateForm(StudentPpdb $studentPpdb)
    {
        if ($studentPpdb->wave()->exists() && !$studentPpdb->form()->exists()) {
            $form = new Form();
            $form->user_id = $studentPpdb->user_id;
            $form->data = $studentPpdb?->school?->ppdbform?->data;
            $form->type = Form::REQUEST_STUDENT_PPDB;
            $form->is_ppdb = true;
            $form->save();

            $studentPpdb->form_id = $form->id;
            $studentPpdb->saveQuietly();
        }
    }

    /**
     * Handle the StudentPpdb "updated" event.
     *
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return void
     */
    public function updated(StudentPpdb $studentPpdb)
    {
        $this->handleCreateForm($studentPpdb);
    }

    /**
     * Handle the StudentPpdb "deleted" event.
     *
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return void
     */
    public function deleted(StudentPpdb $studentPpdb)
    {
        //
    }

    /**
     * Handle the StudentPpdb "restored" event.
     *
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return void
     */
    public function restored(StudentPpdb $studentPpdb)
    {
        //
    }

    /**
     * Handle the StudentPpdb "force deleted" event.
     *
     * @param  \App\Models\StudentPpdb  $studentPpdb
     * @return void
     */
    public function forceDeleted(StudentPpdb $studentPpdb)
    {
        //
    }
}
