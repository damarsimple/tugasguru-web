<?php

namespace App\Models;

use App\Trait\TeacherFollowable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory, TeacherFollowable;

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    public function schools() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\School');
    }
}
