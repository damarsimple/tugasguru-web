<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    public function homeroomteacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher', 'homeroom_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany('App\Models\Student');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher', 'classroom_teacher_subject');
    }

    public function classroomteachersubjects(): HasMany
    {
        return $this->hasMany('App\Models\ClassroomTeacherSubject');
    }
}
