<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentAssigment extends Model
{
    use HasFactory;

    protected $with = ['attachments', 'student'];

    protected $fillable = ['assigment_id', 'student_id'];

    public function assigment(): BelongsTo
    {
        return $this->belongsTo('App\Models\Assigment');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany('App\Models\Attachment', 'attachable');
    }
}
