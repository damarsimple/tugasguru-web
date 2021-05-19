<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'classroom_id',
        'attendable_id',
        'attendable_type'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }
}
