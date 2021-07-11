<?php

namespace App\Trait;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Sociable
{
    public function likes(): MorphMany
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany('App\Models\Comment', "commentable");
    }

    public function getIsLikedAttribute(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getMorphableAttribute(): string
    {
        return static::class;
    }
}
