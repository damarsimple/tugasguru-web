<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examresult extends Model
{
    use HasFactory;

    public function exam(): BelongsTo
    {
        return $this->belongsTo('App\Models\Exam');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function examsession(): BelongsTo
    {
        return $this->belongsTo('App\Models\Examsession');
    }

    public function studentanswers(): HasMany
    {
        return $this->hasMany('App\Models\Studentanswer');
    }
}
