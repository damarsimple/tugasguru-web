<?php

namespace App\Observers;

use App\Enum\Constant;
use App\Misc\AppConfig;
use App\Models\Attendance;
use App\Models\Booking;
use App\Models\Transaction;
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

        if ($attendance->is_bimbel && $attendance->attended) {

            $booking = $attendance->agenda->agendaable;


            $baseAdminTransaction = $booking->transactions()
                ->whereNotNull('transaction_id')
                ->whereHas('user', function ($e) {
                    return $e->where('is_admin', true);
                })->first(); // admin transaction

            $admin = User::where('is_admin', true)->first();

            if (!$admin) return;

            $adminFee = (new AppConfig)->get(Constant::BOOKING_ADMIN_FEE);

            $adminTransaction = new Transaction();

            $adminTransaction->uuid = Str::uuid();

            $adminTransaction->from = $admin->balance;

            $adminTransaction->to = ($admin->balance - $booking->rate) * $adminFee;

            $adminTransaction->payment_method  = Transaction::BALANCE;

            $adminTransaction->transaction_id = $baseAdminTransaction->id;

            $adminTransaction->transactionable_id = $baseAdminTransaction->transactionable_id;

            $adminTransaction->transactionable_type = $baseAdminTransaction->transactionable_type;

            $adminTransaction->amount = $booking->rate;

            $adminTransaction->description =
                str_replace('Kepada Admin', 'Kepada Guru', $baseAdminTransaction->description . " dengan Fee sebesar " . $adminFee * 100 . "%");

            $adminTransaction->is_paid = true;

            $adminTransaction->status = Transaction::SUCCESS;
            $adminTransaction->user_id = $admin->id;

            $adminTransaction->saveQuietly();

            $teacher = $booking->teacher;

            $transaction = new Transaction();

            $transaction->amount = $booking->rate;


            $transaction->uuid = Str::uuid();

            $transaction->payment_method = 'BALANCE';

            $transaction->transaction_id = $adminTransaction->id;

            $transaction->transactionable_id = $booking->id;
            $transaction->transactionable_type = $booking::class;
            $transaction->description = 'Pembayaran Bimbel dari admin Tugasguru ' . $admin->name . ' sebesar ' . $transaction->amount  . " untuk bimbel " . $booking->user->name . " dengan Fee sebesar " . $adminFee * 100 . "%";

            $transaction->staging_url = null;

            $transaction->from = $teacher->balance;

            $transaction->to = $teacher->balance + $adminTransaction->amount;

            $transaction->is_paid = true;

            $transaction->status = Transaction::SUCCESS;

            $transaction->user_id = $teacher->id;

            $transaction->saveQuietly();

            $teacher->balance += $transaction->amount;

            $teacher->save();
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
