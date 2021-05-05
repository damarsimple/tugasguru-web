<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examtracker extends Model
{
    use HasFactory;

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    protected $fillable = [
        'exam_id',
        'student_id',
        'examsession_id'
    ];

    public function examsessions(): BelongsTo
    {
        return $this->belongsTo('App\Models\Examsession');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo('App\Models');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }
}
