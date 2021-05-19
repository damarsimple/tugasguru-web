<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Exam extends Model
{
    use HasFactory;

    public $with = ['teacher', 'subject', 'examtype', 'classroom'];

    public function examsessions(): HasMany
    {
        return $this->hasMany('App\Models\Examsession');
    }

    public function examresults(): HasMany
    {
        return $this->hasMany('App\Models\Examresult');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Question');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Models\User',
            'exam_supervisor',
            'user_id',
            'exam_id',
            'id',
            'id'
        );
    }


    public function examtype(): BelongsTo
    {
        return $this->belongsTo('App\Models\Examtype');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'teacher_id');
    }

    public function attendances() : MorphMany
    {
        return $this->morphMany('App\Models\Attachment', 'attendable');
    }
}
