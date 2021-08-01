<?php

namespace App\Observers;

use App\Models\Agenda;
use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function created(Booking $booking)
    {
        $agenda = new Agenda();

        $agenda->agendaable_id = $booking->id;
        $agenda->agendaable_type = Booking::class;
        $agenda->user_id = $booking->user_id;
        $agenda->name = "Absensi Bimbingan Belajar " . $booking->teacher->name;
        $agenda->save();

        foreach (CarbonPeriod::create($booking->start_at, $booking->finish_at) as $date) {
            Attendance::firstOrCreate([
                "name" => "Absensi Bimbel Tanggal  " . $date->format('d-m'),
                "user_id" => $booking->teacher_id,
                "agenda_id" => $agenda->id,
                "date" => $date,
                "is_bimbel" => true,
            ]);
        }
    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
    }

    /**
     * Handle the Booking "deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function deleted(Booking $booking)
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function restored(Booking $booking)
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function forceDeleted(Booking $booking)
    {
        //
    }
}
