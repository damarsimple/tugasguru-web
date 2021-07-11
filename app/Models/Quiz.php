<?php

namespace App\Models;

use App\Trait\Sociable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Quiz extends Model
{
    use HasFactory, Sociable;

    public const THUMBNAIL = "THUMBNAIL";

    public function quizreward(): HasOne
    {
        return $this->hasOne("App\Models\QuizReward");
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Question");
    }

    public function firstquestion()
    {
        return $this->belongsToMany("App\Models\Question")->take(1);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo("App\Models\Subject");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function thumbnail(): MorphOne
    {
        return $this->morphOne("App\Models\Attachment", "attachable")->where(
            "role",
            self::THUMBNAIL
        );
    }

    public function rooms(): MorphMany
    {
        return $this->morphMany("App\Models\Room", "roomable");
    }
}
