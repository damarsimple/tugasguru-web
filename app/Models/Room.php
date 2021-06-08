<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Room extends Model
{
    use HasFactory;

    protected $with = ["users"];

    public function messages()
    {
        return $this->morphMany("App\Models\Message", "messageable");
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")->withPivot(
            "is_administrator"
        );
    }

    public function roomable(): MorphTo
    {
        return $this->morphTo();
    }

    public function quizresults(): HasMany
    {
        return $this->hasMany("App\Models\Quizresult");
    }
}
