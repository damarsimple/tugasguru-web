<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    public $with = ['subjects', 'schooltype'];

    public function teachers(): HasMany
    {
        return $this->hasMany('App\Models\Teacher');
    }

    public function students(): HasMany
    {
        return $this->hasMany('App\Models\User')->where('roles', User::STUDENT);
    }

    public function partteacher(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher');
    }
    public function province(): BelongsTo
    {
        return $this->belongsTo('App\Models\Province');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo('App\Models\City');
    }

    public function schooltype(): BelongsTo
    {
        return $this->belongsTo('App\Models\Schooltype');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject');
    }

    public function classtypes(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Classtype');
    }


    public function classrooms(): HasMany
    {
        return $this->hasMany('App\Models\Classroom');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo('App\Models\District');
    }
}
