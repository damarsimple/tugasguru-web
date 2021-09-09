<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Exam extends Model
{
    use HasFactory;

    public $with = ["user", "subject", "examtype", "classroom"];

    public function examsessions(): HasMany
    {
        return $this->hasMany("App\Models\Examsession");
    }

    public function examresults(): HasMany
    {
        return $this->hasMany("App\Models\Examresult");
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Question");
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo("App\Models\Subject");
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo("App\Models\Classroom");
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(
            "App\Models\User",
            "exam_supervisor",
            "exam_id",
            "user_id",
        );
    }

    public function examtype(): BelongsTo
    {
        return $this->belongsTo("App\Models\Examtype");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function agenda(): MorphOne
    {
        return $this->morphOne("App\Models\Agenda", "agendaable");
    }
}
