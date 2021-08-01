<?php

namespace App\Observers;

use App\Models\Agenda;
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
        $studentUserIds = $exam->classroom->students->pluck("id");

        $absents = $exam->user
            ->studentabsents()
            ->whereDate("finish_at", ">", now())
            ->get();

        $absentsMap = [];

        foreach ($absents as $value) {
            $absentsMap[$value->user_id] = $value;
        }


        $agenda = new Agenda();

        $agenda->agendaable_id = $exam->id;
        $agenda->agendaable_type = Exam::class;
        $agenda->user_id = $exam->user_id;
        $agenda->name = "Absensi " . $exam->name . "";
        $agenda->school_id = $exam->classroom->school_id;
        $agenda->save();
        foreach ($studentUserIds as $id) {
            if (array_key_exists($id, $absentsMap)) {
                Attendance::firstOrCreate([
                    "school_id" => $exam->classroom->school_id,
                    "user_id" => $id,
                    "attended" => false,
                    "reason" => $absentsMap[$id]->reason,
                    "agenda_id" => $agenda->id
                ]);
            } else {
                Attendance::firstOrCreate([
                    "school_id" => $exam->classroom->school_id,
                    "user_id" => $id,
                    "agenda_id" => $agenda->id
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
