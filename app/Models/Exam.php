<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    public function examsessions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\ExamSession');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Question');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Subject');
    }


    public function clasrooms(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Clasroom');
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Models\Teacher',
            'exam_supervisor',
            'supervisor_id',
            'exam_id',
            'id',
            'id'
        );
    }


    public function examtype(): BelongsTo
    {
        return $this->belongsTo('App\Models\Exam');
    }
}
