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

    public $with = ['classtype'];

    public $appends = ['name_formatted'];
    public function students(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Student');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function exams(): HasMany
    {
        return $this->hasMany('App\Models\Exam');
    }

    public function classtype(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classtype');
    }

    public function getNameFormattedAttribute()
    {
        return "Kelas "  . $this->classtype->level . " $this->name";
    }

    public function meetings(): HasMany
    {
        return $this->hasMany('App\Models\Meeting');
    }

    public function assigments(): HasMany
    {
        return $this->hasMany('App\Models\Assigment');
    }
}
