<?php

namespace App\Models;

use App\Trait\Attachable;
use App\Trait\Sociable;
use App\Trait\Thumbnailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, Attachable, Sociable, Thumbnailable;

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo("App\Models\Subject");
    }

    public function classtype(): BelongsTo
    {
        return $this->belongsTo("App\Models\Classtype");
    }

    public function videos(): HasMany
    {
        return $this->hasMany('App\Models\Video');
    }
}
