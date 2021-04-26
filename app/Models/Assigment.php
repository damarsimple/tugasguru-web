<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assigment extends Model
{
    use HasFactory;


    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('Appp\Models\Subject');
    }

    public function classroomschools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ClassRoomSchool');
    }

}
