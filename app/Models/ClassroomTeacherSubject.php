<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomTeacherSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'classroom_id',
    ];
    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }
    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }
    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }
}
