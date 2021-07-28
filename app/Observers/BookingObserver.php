<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Transaction;
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
        //
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

            $transaction = new Transaction();

            $transaction->amount = 50000;

            $transaction->payment_method = 'BALANCE';

            $transaction->transactionable_id = $booking->id;
            $transaction->transactionable_type = $booking::class;
            $transaction->description = 'Pembayaran Bimbel dari' . $booking->user->name . ' sebesar ' . $transaction->amount;

            $transaction->staging_url = null;

            $transaction->is_paid = true;
            $transaction->status = Transaction::SUCCESS;

            $booking->teacher()->transactions()->save($transaction);

            $user  = $booking->teacher;

            $user->balance += $transaction->amount;

            $user->save();
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
