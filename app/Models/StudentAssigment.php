<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentAssigment extends Model
{
    use HasFactory;

    protected $with = ["attachments", "user"];

    protected $fillable = ["assigment_id", "user_id"];

    public function assigment(): BelongsTo
    {
        return $this->belongsTo("App\Models\Assigment");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany("App\Models\Attachment", "attachable");
    }
}
