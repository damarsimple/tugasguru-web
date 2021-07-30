<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Transactionable
{
    public function transactions(): MorphMany
    {
        return $this->morphMany('App\Models\Transaction', 'transactionable');
    }
}
