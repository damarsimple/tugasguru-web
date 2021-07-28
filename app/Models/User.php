<?php

namespace App\Models;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder as EBuilder;
use Illuminate\Database\Query\Builder as QBuilder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

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
        'identity',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'hidden_attribute' => 'array',
        'access' => 'array',
        'video_access_limit' => 'array',
        'identity' => 'array'
    ];

    public const PROFILEPICTURE = 'PROFILEPICTURE';
    public const DOCUMENTS  = 'DOCUMENTS';

    public const STUDENT = 'STUDENT';
    public const GUARDIAN = 'GUARDIAN';
    public const TEACHER = 'TEACHER';
    public const BIMBEL = 'BIMBEL';
    public const GENERAL = 'GENERAL';
    public const ADMIN = 'ADMIN';
    public const STUDENT_PPDB = 'STUDENT_PPDB';

    protected $appends = ['mainschool'];

    protected $with = ['profilepicture'];

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

    public function adminschools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School')->wherePivot('is_administrator', true);
    }

    public function counselorschools(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\School')->wherePivot('is_counselor', true);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject')->withPivot('kkm');;
    }

    public function exams(): HasMany
    {
        return $this->hasMany('App\Models\Exam', 'user_id');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany('App\Models\Classroom', 'user_id');
    }

    public function assigments(): HasMany
    {
        return $this->hasMany('App\Models\Assigment');
    }


    public function meetings(): HasMany
    {
        return $this->hasMany('App\Models\Meeting', 'user_id');
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
        return $this->hasMany('App\Models\Absent', foreignKey: 'receiver_id');
    }

    public function studentanswers()
    {
        return $this->hasMany('App\Models\StudentAnswer');
    }

    public function getMainschoolAttribute(): School|null
    {
        if ($this->roles == self::TEACHER) {
            return $this->schools()->first();
        }

        return null;
    }

    public function studentconsultations(): HasMany
    {
        return $this->hasMany('App\Models\Consultation', foreignKey: 'consultant_id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany('App\Models\Form');
    }

    public function reports(): HasMany
    {
        return $this->hasMany('App\Models\Report');
    }

    public function accesses(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Access')->wherePivot('expired_at', '>', now())->withPivot('expired_at');
    }

    public function rawaccesses(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Access')->withPivot('expired_at');
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

    public function getAdminSchoolIdAttribute(): string | null
    {
        return $this?->adminschools()?->first()?->id;
    }

    public function extracurriculars(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Extracurricular");
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo("App\Models\Major");
    }

    public function studentppdbs(): HasMany
    {
        return $this->hasMany('App\Models\StudentPpdb');
    }

    public function comments(): HasMany
    {
        return $this->hasMany('App\Models\Comment');
    }
    public function likes(): HasMany
    {
        return $this->hasMany('App\Models\Like');
    }

    public function autosaves(): HasMany
    {
        return $this->hasMany('App\Models\Autosave');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany('App\Models\Booking');
    }

    public function mybookings(): HasMany
    {
        return $this->hasMany('App\Models\Booking', foreignKey: 'teacher_id');
    }

    public function bimbels($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): QBuilder|EBuilder
    {
        $builder = User::getQuery();

        if (array_key_exists('subject_id', $args)) {
            $builder = User::whereHas('subjects', function ($q) use ($args) {
                return $q->where('subjects.id', $args['subject_id']);
            });
        }

        return $builder;
    }
}
