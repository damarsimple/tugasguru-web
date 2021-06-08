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

    public $with = ['user', 'school', 'subjects'];

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

   

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User')->where('is_accepted', true);
    }

    public function requestfollow(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User')->where('is_accepted', false);
    }

  
}
