<?php

namespace App\Models;

use App\Trait\TeacherFollowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Student extends Model
{
    use HasFactory, TeacherFollowable;

    public $with = ['user', 'school'];

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Classroom');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'parent_id');
    }

    public function events(): MorphMany
    {
        return $this->morphMany('App\Models\Event', 'eventable');
    }
}
