<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    public function examsessions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher', 'exam_supervisor', 'teacher_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Question');
    }
}
