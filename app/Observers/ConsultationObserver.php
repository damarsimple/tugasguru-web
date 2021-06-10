<?php

namespace App\Observers;

use App\Models\Consultation;
use App\Notifications\StudentCreateConsultation;
use App\Notifications\TeacherRespondConsultation;

class ConsultationObserver
{
    /**
     * Handle the Consultation "created" event.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return void
     */
    public function created(Consultation $consultation)
    {
        $consultation->consultant->notify(
            new StudentCreateConsultation($consultation)
        );
    }

    /**
     * Handle the Consultation "updated" event.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return void
     */
    public function updated(Consultation $consultation)
    {
        $consultation->user->notify(
            new TeacherRespondConsultation($consultation)
        );
    }

    /**
     * Handle the Consultation "deleted" event.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return void
     */
    public function deleted(Consultation $consultation)
    {
        //
    }

    /**
     * Handle the Consultation "restored" event.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return void
     */
    public function restored(Consultation $consultation)
    {
        //
    }

    /**
     * Handle the Consultation "force deleted" event.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return void
     */
    public function forceDeleted(Consultation $consultation)
    {
        //
    }
}
