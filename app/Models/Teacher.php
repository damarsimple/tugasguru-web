<?php

namespace App\Models;

use App\Trait\TeacherFollowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, TeacherFollowable;

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
        return $this->belongsToMany('App\Models\Subject');
    }

    public function questions(): HasMany
    {
        return $this->hasMany('App\Models\Question');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany('App\Models\Classroom', 'homeroom_id');
    }

    public function classroomteachersubjects(): HasMany
    {
        return $this->hasMany('App\Models\ClassroomTeacherSubject');
    }
}
