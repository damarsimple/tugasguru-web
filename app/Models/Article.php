<?php

namespace App\Models;

use App\Trait\Sociable;
use App\Trait\Thumbnailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Article extends Model
{
    use HasFactory, Sociable, Thumbnailable;

    public const ANNOUNCEMENT = "ANNOUNCEMENT";
    public const THEORY = "THEORY";
    public const POST = "POST";

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function price()
    {
        return $this->morphOne("App\Models\Price", "priceable");
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Subject");
    }

    public function classtypes(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\Classtype");
    }

    public function meetings(): HasMany
    {
        return $this->hasMany("App\Models\Meeting");
    }
}
