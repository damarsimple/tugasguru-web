<?php

namespace App\Models;

use App\Trait\TeacherFollowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Teacher extends Model
{
    use HasFactory, TeacherFollowable;

    public $with = ['user'];

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject')->withPivot('kkm');;
    }

    public function questions(): HasMany
    {
        return $this->hasMany('App\Models\Question');
    }

    public function exams(): HasMany
    {
        return $this->hasMany('App\Models\Exam');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany('App\Models\Classroom');
    }

    public function packagequestions(): HasMany
    {
        return $this->hasMany('App\Models\Packagequestion');
    }

    public function articles(): HasMany
    {
        return $this->hasMany('App\Models\Article');
    }
}
