<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory;

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function answers(): HasMany
    {
        return $this->hasMany('App\Models\Answer');
    }

    public function correctanswer(): HasOne
    {
        return $this->hasOne('App\Models\Answer')->where('is_correct', true);
    }


    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('Appp\Models\Subject');
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ClassRoom');
    }
}
