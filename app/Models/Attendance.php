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
        'student_id',
        'teacher_id',
        'subject_id',
        'classroom_id',
        'attendable_id',
        'attendable_type'
    ];
    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }
}
