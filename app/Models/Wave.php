<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wave extends Model
{
    use HasFactory;

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }
}
