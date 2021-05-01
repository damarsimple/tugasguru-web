<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function price()
    {
        return $this->morphOne('App\Models\Price', 'priceable');
    }

    public function thumbnail()
    {
        return $this->morphOne('App\Models\Attachment', 'attachable');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject');
    }

    public function classtypes(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Classtype');
    }
}
