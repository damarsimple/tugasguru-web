<?php

namespace App\Actions\Attachment;

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

        try {
            $attachment->mime =  $file->getMimeType();
        } catch (\Throwable $th) {
            $attachment->mime = self::mimeGuesser($file->getClientOriginalExtension()) ?? "unknown";
        }
        $attachment->is_proccessed = $isProcessed;
        $attachment->original_size = $originalSize;
        $attachment->compressed_size = $compressedSize;

        request()->user()->attachments()->save($attachment);

        $file->move('attachments', $attachment->name);

        return $attachment;
    }

    private static function mimeGuesser($ext)
    {
        $mime =  [
            "doc"   => " application/msword",
            "dot"   => "application/msword",

            "docx"  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            "dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
            "docm" => "application/vnd.ms-word.document.macroEnabled.12",
            "dotm" =>   'application/vnd.ms-word.template.macroEnabled.12',

            "xls"  => 'application/vnd.ms-excel',
            "xlt"   =>  "application/vnd.ms-excel",
            "xla"  =>  "application/vnd.ms-excel",

            "xlsx" =>  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "xltx"  => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
            'xlsm' => "application/vnd.ms-excel.sheet.macroEnabled.12",
            "xltm" => "application/vnd.ms-excel.template.macroEnabled.12",
            "xlam"   => "application/vnd.ms-excel.addin.macroEnabled.12",
            "xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",

            "ppt" => "application/vnd.ms-powerpoint",
            "pot"  => "application/vnd.ms-powerpoint",
            'pps'    => "application/vnd.ms-powerpoint",
            "ppa"   => "application/vnd.ms-powerpoint",

            "pptx"  => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "potx" =>  "application/vnd.openxmlformats-officedocument.presentationml.template",
            "ppsx"  =>  "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
            "ppam" =>  "application/vnd.ms-powerpoint.addin.macroEnabled.12",
            "pptm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
            "potm"   => "application/vnd.ms-powerpoint.template.macroEnabled.12",
            'ppsm'  => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12",

            "mdb"   =>  "application/vnd.ms-access",
        ];

        try {
            return $mime[$ext];
        } catch (\Throwable $th) {
            return null;
        }
    }
}
