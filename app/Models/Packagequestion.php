<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Packagequestion extends Model
{
    use HasFactory;

    public $with = [
        'teacher',
        'subject',
        'questions.answers.attachment',
        'questions.attachments'
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Question');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    public function classtype(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classtype');
    }
}
