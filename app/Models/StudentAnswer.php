<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'question_id',
        'student_id',
        'exam_id',
        'examsession_id'
    ];

    public function answer(): BelongsTo
    {
        return $this->belongsTo('App\Models\Answer');
    }
    public function question(): BelongsTo
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function examsession(): BelongsTo
    {
        return $this->belongsTo('App\Models\Examsession');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }
    public function exam(): BelongsTo
    {
        return $this->belongsTo('App\Models\Exam');
    }
}
