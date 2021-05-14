<?php

namespace App\Observers;

use App\Jobs\ProcessAttachmentJob;
use App\Models\Attachment;

class AttachmentObserver
{
    /**
     * Handle the Attachment "created" event.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return void
     */
    public function created(Attachment $attachment)
    {
        if (!$attachment->is_proccessed) {
            dispatch(new ProcessAttachmentJob($attachment));
        }
    }

    /**
     * Handle the Attachment "updated" event.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return void
     */
    public function updated(Attachment $attachment)
    {
        //
    }

    /**
     * Handle the Attachment "deleted" event.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return void
     */
    public function deleted(Attachment $attachment)
    {
        try {
            unlink(public_path('attachments') . "\\" . $attachment->name);
        } catch (\Throwable $th) {
        }
    }

    /**
     * Handle the Attachment "force deleted" event.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return void
     */
    public function forceDeleted(Attachment $attachment)
    {
        try {
            unlink(public_path('attachments') . "\\" . $attachment->name);
        } catch (\Throwable $th) {
        }
    }

    /**
     * Handle the Attachment "restored" event.
     *
     * @param  \App\Models\Attachment  $attachment
     * @return void
     */
    public function restored(Attachment $attachment)
    {
        //
    }
}
