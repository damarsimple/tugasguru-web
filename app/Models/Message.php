<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    use HasFactory;

    protected $with = ["user"];

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }
}
