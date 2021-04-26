<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    public function province(): BelongsTo
    {
        return $this->belongsTo('App\Models\Province');
    }

    public function schools(): HasMany
    {
        return $this->hasMany('App\Models\School');
    }
}
