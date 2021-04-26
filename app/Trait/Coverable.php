<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait Coverable
{
    public function cover(): HasOne
    {
        return $this->hasOne('App\Models\Photo');
    }
}
