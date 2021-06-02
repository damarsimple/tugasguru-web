<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    use HasFactory;

    public function quizresult(): BelongsTo
    {
        return $this->belongsTo('App\Models\Quizresult');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo('App\Models\Answer');
    }
}
