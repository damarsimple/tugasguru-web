<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait TeacherFollowable
{

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher')->wherePivot('is_accepted', true);
    }
    public function pendingteachers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher')->wherePivot('is_accepted', false);
    }

    public function rejectedteachers(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher')->wherePivot('is_rejected', true);
    }
}
