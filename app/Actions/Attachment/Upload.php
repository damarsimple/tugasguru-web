<?php

namespace App\Actions\Attachment;

use App\Jobs\ProcessAttachmentJob;
use App\Models\Attachment;
use Illuminate\Support\Str;

class Upload
{

    public static function handle(\Illuminate\Http\UploadedFile $file)
    {
        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . $file->getClientOriginalExtension();
        $attachment->mime = $file->getClientMimeType();

        request()->user()->attachments()->save($attachment);

        $file->move('attachments', $attachment->name);

        dispatch(new ProcessAttachmentJob($attachment));

        return $attachment;
    }
}
