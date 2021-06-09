<?php

namespace App\Observers;

use App\Models\StudentAssigment;
use App\Notifications\StudentTurningAssigment;
use App\Notifications\TeacherGradeAssigment;

class StudentAssigmentObserver
{
    /**
     * Handle the StudentAssigment "created" event.
     *
     * @param  \App\Models\StudentAssigment  $studentAssigment
     * @return void
     */
    public function created(StudentAssigment $studentAssigment)
    {
        $studentAssigment->assigment->user->notify(
            new StudentTurningAssigment($studentAssigment)
        );
    }

    /**
     * Handle the StudentAssigment "updated" event.
     *
     * @param  \App\Models\StudentAssigment  $studentAssigment
     * @return void
     */
    public function updated(StudentAssigment $studentAssigment)
    {
        if ($studentAssigment->grade != 0) {
            $studentAssigment->user->notify(
                new TeacherGradeAssigment($studentAssigment)
            );
        }
    }

    /**
     * Handle the StudentAssigment "deleted" event.
     *
     * @param  \App\Models\StudentAssigment  $studentAssigment
     * @return void
     */
    public function deleted(StudentAssigment $studentAssigment)
    {
        //
    }

    /**
     * Handle the StudentAssigment "restored" event.
     *
     * @param  \App\Models\StudentAssigment  $studentAssigment
     * @return void
     */
    public function restored(StudentAssigment $studentAssigment)
    {
        //
    }

    /**
     * Handle the StudentAssigment "force deleted" event.
     *
     * @param  \App\Models\StudentAssigment  $studentAssigment
     * @return void
     */
    public function forceDeleted(StudentAssigment $studentAssigment)
    {
        //
    }
}
