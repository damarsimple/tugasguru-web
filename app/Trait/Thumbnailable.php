<?php

namespace App\Trait;

use App\Enum\Constant;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Thumbnailable
{
    public function thumbnail(): MorphOne
    {
        return $this->morphOne("App\Models\Attachment", "attachable")->where(
            "role",
            Constant::THUMBNAIL
        );
    }
}
