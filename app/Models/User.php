<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'province_id',
        'city_id',
        'district_id',
        'phone',
        'roles'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'hidden_attribute' => 'array',
        'access' => 'array'
    ];

    public const PROFILEPICTURE = 'PROFILEPICTURE';
    public const STUDENT = 'STUDENT';
    public const TEACHER = 'TEACHER';
    protected $appends = ['mainschool'];

    protected $with = ['profilepicture', 'province'];

    public function province(): BelongsTo
    {
        return $this->belongsTo('App\Models\Province');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo('App\Models\City');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo('App\Models\District');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany('App\Models\User', 'parent_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany('App\Models\Attachment');
    }

    public function articles(): HasMany
    {
        return $this->hasMany('App\Models\Article');
    }

    public function frontarticles(): HasMany
    {
        return $this->hasMany('App\Models\Article')->take(10)->latest();
    }


    // function followingteachers(): BelongsToMany
    // {
    //     return $this->belongsToMany('App\Models\User')->where('is_accepted', true);
    // }

    // function followingstudents(): belongsToMany
    // {
    //     return $this->belongsToMany('App\Models\Student')->where('is_accepted', true);
    // }

    // function requestfollowingteachers(): BelongsToMany
    // {
    //     return $this->belongsToMany('App\Models\User')->where('is_accepted', false);
    // }

    // function requestfollowingstudents(): belongsToMany
    // {
    //     return $this->belongsToMany('App\Models\Student')->where('is_accepted', false);
    // }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', relatedPivotKey: 'follower_id', foreignPivotKey: 'user_id')->where('is_accepted', true);
    }

    public function requestfollowers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', relatedPivotKey: 'follower_id', foreignPivotKey: 'user_id')->where('is_accepted', false);
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', relatedPivotKey: 'user_id', foreignPivotKey: 'follower_id')->where('is_accepted', true);
    }

    public function requestfollowings(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', relatedPivotKey: 'user_id', foreignPivotKey: 'follower_id')->where('is_accepted', false);
    }


    public function getFollowingCountAttribute()
    {
        return $this->followingteachers()->count() + $this->followingstudents()->count();
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Room')->withPivot('is_administrator');
    }

    public function profilepicture()
    {
        return $this->morphOne('App\Models\Attachment', 'attachable')->where('role', self::PROFILEPICTURE);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    // student


    public function myclassrooms(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Classroom');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'parent_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany('App\Models\Consultation');
    }

    public function absents(): HasMany
    {
        return $this->hasMany('App\Models\Absent');
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School');
    }

    public function homeroomschools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School')->wherePivot('is_homeroom', true);
    }

    public function headmasterschools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School')->wherePivot('is_headmaster', true);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject')->withPivot('kkm');;
    }



    public function exams(): HasMany
    {
        return $this->hasMany('App\Models\Exam', 'teacher_id');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany('App\Models\Classroom', 'teacher_id');
    }

    public function assigments(): HasMany
    {
        return $this->hasMany('App\Models\Assigment', 'teacher_id');
    }


    public function meetings(): HasMany
    {
        return $this->hasMany('App\Models\Meeting', 'teacher_id');
    }

    public function packagequestions(): HasMany
    {
        return $this->hasMany('App\Models\Packagequestion');
    }

    public function questions(): HasMany
    {
        return $this->hasMany('App\Models\Question');
    }

    public function examresults(): HasMany
    {
        return $this->hasMany('App\Models\Examresult');
    }

    public function studentassigments(): HasMany
    {
        return $this->hasMany('App\Models\StudentAssigment');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany('App\Models\Attendance');
    }

    public function studentabsents(): HasMany
    {
        return $this->hasMany('App\Models\Absent', foreignKey: 'teacher_id');
    }

    public function studentanswers()
    {
        return $this->hasMany('App\Models\StudentAnswer');
    }

    public function getMainschoolAttribute(): School|null
    {
        if ($this->roles == self::TEACHER)
            return $this->schools()->first();

        return null;
    }

    public function studentconsultations(): HasMany
    {
        return $this->hasMany('App\Models\Consultation', foreignKey: 'teacher_id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany('App\Models\Form');
    }

    public function reports(): HasMany
    {
        return $this->hasMany('App\Models\Report');
    }

    public function studentattendances(): HasManyThrough
    {
        return $this->hasManyThrough('App\Models\Attendance', 'App\Models\Classroom', firstKey: 'teacher_id');
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subscription')->wherePivot('expired_at', '>', now())->withPivot('expired_at');
    }

    public function rawsubscriptions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subscription')->withPivot('expired_at');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function myreports(): BelongsToMany
    {
    return $this->belongsToMany('App\Models\Report');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany('App\Models\Quiz');
    }

    public function agendas(): HasMany
    {
        return $this->hasMany('App\Models\Agenda');
    }
}
