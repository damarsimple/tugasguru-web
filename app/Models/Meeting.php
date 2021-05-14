<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Meeting extends Model
{
    use HasFactory;

    protected $with = ['rooms', 'classroom.students', 'subject', 'teacher'];

    protected $casts  = ['data' => 'object'];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo('App\Models\Article');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }

    function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function rooms(): MorphMany
    {
        return $this->morphMany('App\Models\Room', 'roomable');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'attachable');
    }
}
