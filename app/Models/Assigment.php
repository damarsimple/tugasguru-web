<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assigment extends Model
{
    use HasFactory;

    // protected $with = ['teacher', 'classroom', 'subject'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function subject(): belongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    public function classroom(): belongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function studentassigments(): HasMany
    {
        return $this->hasMany('App\Models\StudentAssigment');
    }

    public function myanswer(): HasOne
    {
        return $this->hasOne('App\Models\StudentAssigment')->where('user_id', request()->user()->student?->id);
    }
}
