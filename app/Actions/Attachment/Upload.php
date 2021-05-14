<?php

namespace App\Actions\Attachment;

use App\Jobs\ProcessAttachmentJob;
use App\Models\Attachment;
use Illuminate\Support\Str;

class Upload
{

    public static function handle(
        \Illuminate\Http\UploadedFile $file,
        bool $isProcessed = false,
        int $originalSize = 0,
        int $compressedSize = 0,
    ) {
        $attachment = new Attachment();

        $attachment->name = Str::uuid() . "." . $file->getClientOriginalExtension();
        $attachment->mime =  $file->getMimeType();
        $attachment->is_proccessed = $isProcessed;
        $attachment->original_size = $originalSize;
        $attachment->compressed_size = $compressedSize;

        request()->user()->attachments()->save($attachment);

        $file->move('attachments', $attachment->name);

        return $attachment;
    }
}
