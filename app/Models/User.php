<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'hidden_attribute' => 'array'
    ];

    public const PROFILEPICTURE = 'PROFILEPICTURE';

    // protected $appends = ['following_count'];

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

    public function student(): HasOne
    {
        return $this->hasOne('App\Models\Student');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany('App\Models\User', 'parent_id');
    }

    public function teacher(): HasOne
    {
        return $this->hasOne('App\Models\Teacher');
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


    function followingteachers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher')->where('is_accepted', true);
    }

    function followingstudents(): belongsToMany
    {
        return $this->belongsToMany('App\Models\Student')->where('is_accepted', true);
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
}
