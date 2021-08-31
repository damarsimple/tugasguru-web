<?php

namespace App\GraphQL\Mutations;

use App\Actions\Attachment\Upload;

class UploadAttachment
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $args['file'];

        return Upload::handle($file);

        return $file->storePublicly('uploads');
    }
}
