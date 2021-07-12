<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class School extends Model
{
    use HasFactory;

    public const LOGO = 'LOGO';
    public const COVER = 'COVER';
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")->where(
            "roles",
            User::TEACHER
        );
    }


    public function announcements(): HasMany
    {

        return $this->hasMany("App\Models\Article")->where('role', Article::ANNOUNCEMENT);
    }

    public function homeroomteachers(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")
            ->where("roles", User::TEACHER)
            ->wherePivot("is_homeroom", true);
    }

    public function headmasters(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")
            ->where("roles", User::TEACHER)
            ->wherePivot("is_headmaster", true);
    }
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")
            ->where("roles", User::TEACHER)
            ->wherePivot("is_administrator", true);
    }

    public function counselors(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User")
            ->where("roles", User::TEACHER)
            ->wherePivot("is_counselor", true);
    }

    public function logo()
    {
        return $this->morphOne('App\Models\Attachment', 'attachable')->where('role', self::LOGO);
    }

    public function cover()
    {
        return $this->morphOne('App\Models\Attachment', 'attachable')->where('role', self::COVER);
    }

    public function ppdbform(): BelongsTo
    {
        return $this->belongsTo('App\Models\FormTemplate', 'form_template_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany("App\Models\User")->where("roles", User::STUDENT);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo("App\Models\Province");
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo("App\Models\City");
    }

    public function schooltype(): BelongsTo
    {
        return $this->belongsTo("App\Models\Schooltype");
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Subject");
    }

    public function majors(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Major");
    }

    public function extracurriculars(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Extracurricular");
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany("App\Models\Classroom");
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo("App\Models\District");
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            "App\Models\Attendance",
        );
    }

    public function waves(): HasMany
    {
        return $this->hasMany('App\Models\Wave');
    }

    public function openWaves(): HasMany
    {
        return $this->hasMany('App\Models\Wave')->where('close_at', '>', now());
    }

    public function studentppdbs(): HasMany
    {
        return $this->hasMany("App\Models\StudentPpdb")->latest();
    }


    public function hasWave(Builder $builder): Builder
    {
        return $builder->whereHas('openWaves');
    }
}
