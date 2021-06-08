<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    use HasFactory;

    public $with = ["question", "question.answers"];

    protected $fillable = [
        "question_id",
        "user_id",
        "exam_id",
        "examsession_id",
        "examresult_id",
    ];

    public function answer(): BelongsTo
    {
        return $this->belongsTo("App\Models\Answer");
    }
    public function question(): BelongsTo
    {
        return $this->belongsTo("App\Models\Question");
    }

    public function examsession(): BelongsTo
    {
        return $this->belongsTo("App\Models\Examsession");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }
    public function exam(): BelongsTo
    {
        return $this->belongsTo("App\Models\Exam");
    }

    public function examresult(): BelongsTo
    {
        return $this->belongsTo("App\Models\Examresult");
    }
}
