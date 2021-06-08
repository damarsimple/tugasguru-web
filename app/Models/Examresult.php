<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examresult extends Model
{
    use HasFactory;

    protected $fillable = ["examsession_id", "user_id", "exam_id"];

    protected $with = ["user"];

    public function exam(): BelongsTo
    {
        return $this->belongsTo("App\Models\Exam");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function examsession(): BelongsTo
    {
        return $this->belongsTo("App\Models\Examsession");
    }

    public function studentanswers(): HasMany
    {
        return $this->hasMany("App\Models\StudentAnswer");
    }
}
