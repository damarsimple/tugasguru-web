<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attachable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany("App\Models\Attachment", "attachable");
    }
}
