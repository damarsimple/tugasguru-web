<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\Exam;

class ExamObserver
{
    /**
     * Handle the Exam "created" event.
     *
     * @param  \App\Models\Exam  $exam
     * @return void
     */
    public function created(Exam $exam)
    {
        $studentUserIds = $exam->classroom->students->pluck('id');

        $absents = $exam->teacher->studentabsents()->whereDate('finish_at', '>', now())->get();

        $absentsMap = [];

        foreach ($absents as $value) {
            $absentsMap[$value->user_id] = $value;
        }

        foreach ($studentUserIds as $id) {
            if (array_key_exists($id, $absentsMap)) {
                Attendance::firstOrCreate([
                    'subject_id' => $exam->subject_id,
                    'classroom_id' => $exam->classroom_id,
                    'user_id' => $id,
                    'attendable_id' => $exam->id,
                    'attendable_type' => Exam::class,
                    'attended' => false,
                    'reason' => $absentsMap[$id]->reason,
                ]);
            } else {
                Attendance::firstOrCreate([
                    'subject_id' => $exam->subject_id,
                    'classroom_id' => $exam->classroom_id,
                    'user_id' => $id,
                    'attendable_id' => $exam->id,
                    'attendable_type' => Exam::class
                ]);
            }
        }
    }

    /**
     * Handle the Exam "updated" event.
     *
     * @param  \App\Models\Exam  $exam
     * @return void
     */
    public function updated(Exam $exam)
    {
        //
    }

    /**
     * Handle the Exam "deleted" event.
     *
     * @param  \App\Models\Exam  $exam
     * @return void
     */
    public function deleted(Exam $exam)
    {
        //
    }

    /**
     * Handle the Exam "restored" event.
     *
     * @param  \App\Models\Exam  $exam
     * @return void
     */
    public function restored(Exam $exam)
    {
        //
    }

    /**
     * Handle the Exam "force deleted" event.
     *
     * @param  \App\Models\Exam  $exam
     * @return void
     */
    public function forceDeleted(Exam $exam)
    {
        //
    }
}
