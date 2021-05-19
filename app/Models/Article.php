<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    public const THUMBNAIL = 'THUMBNAIL';
    public const ANNOUNCEMENT = 'ANNOUNCEMENT';
    public const THEORY = 'THEORY';
    public const POST = 'POST';

    public $with = ['user',  'subjects', 'classtypes', 'price', 'thumbnail'];

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
        return $this->morphOne('App\Models\Attachment', 'attachable')->where('role', self::THUMBNAIL);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject');
    }

    public function classtypes(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Classtype');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany('App\Models\Meeting');
    }
}
