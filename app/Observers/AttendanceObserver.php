<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\AttendanceAttended;
use App\Notifications\NewAttendance;
use Illuminate\Support\Str;

class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function created(Attendance $attendance)
    {
        $attendance->uuid = Str::uuid();
        $attendance->saveQuietly();
        $attendance->user->notify(new NewAttendance($attendance));
    }

    /**
     * Handle the Attendance "updated" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function updated(Attendance $attendance)
    {
        if ($attendance->attended && $attendance->user->roles == User::STUDENT && $attendance->user->guardian) {
            $guardian = $attendance->user->guardian;
            $attendance->agenda = $attendance->agenda;
            $guardian->notify(new AttendanceAttended($attendance));
        }
    }

    /**
     * Handle the Attendance "deleted" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function deleted(Attendance $attendance)
    {
        //
    }

    /**
     * Handle the Attendance "restored" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function restored(Attendance $attendance)
    {
        //
    }

    /**
     * Handle the Attendance "force deleted" event.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return void
     */
    public function forceDeleted(Attendance $attendance)
    {
        //
    }
}
