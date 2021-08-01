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
                "date" => $date
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
        if ($booking->status == Booking::SELESAI) {

            $baseAdminTransaction = $booking->transactions()
                ->whereNotNull('transaction_id')
                ->whereHas('user', function ($e) {
                    return $e->where('is_admin', true);
                })->first(); // admin transaction

            $admin = User::where('is_admin', true)->first();

            if (!$admin) return;

            $adminTransaction = new Transaction();

            $adminTransaction->uuid = Str::uuid();

            $adminTransaction->from = $admin->balance;

            $adminTransaction->to = $admin->balance - $baseAdminTransaction->amount;

            $adminTransaction->payment_method  = Transaction::BALANCE;

            $adminTransaction->transaction_id = $baseAdminTransaction->id;

            $adminTransaction->transactionable_id = $baseAdminTransaction->transactionable_id;

            $adminTransaction->transactionable_type = $baseAdminTransaction->transactionable_type;

            $adminTransaction->amount = $baseAdminTransaction->amount;

            $adminTransaction->description =
                str_replace('Kepada Admin', 'Kepada Guru', $baseAdminTransaction->description);

            $adminTransaction->is_paid = true;

            $adminTransaction->status = Transaction::SUCCESS;
            $adminTransaction->user_id = $admin->id;

            $adminTransaction->saveQuietly();

            $teacher = $booking->teacher;

            $transaction = new Transaction();

            $transaction->amount = $baseAdminTransaction->amount;


            $transaction->uuid = Str::uuid();

            $transaction->payment_method = 'BALANCE';

            $transaction->transaction_id = $adminTransaction->id;

            $transaction->transactionable_id = $booking->id;
            $transaction->transactionable_type = $booking::class;
            $transaction->description = 'Pembayaran Bimbel dari' . $booking->user->name . ' sebesar ' . $transaction->amount;

            $transaction->staging_url = null;

            $adminTransaction->from = $teacher->balance;

            $adminTransaction->to = $teacher->balance + $transaction->amount;

            $transaction->is_paid = true;

            $transaction->status = Transaction::SUCCESS;

            $transaction->user_id = $teacher->id;

            $transaction->saveQuietly();

            $teacher->balance += $transaction->amount;

            $teacher->save();
        }
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
