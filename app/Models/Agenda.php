<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Agenda extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function attendances(): HasMany
    {
        return $this->hasMany("App\Models\Attendance");
    }

    public function agendaable(): MorphTo
    {
        return $this->morphTo();
    }
}
