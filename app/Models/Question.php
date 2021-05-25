<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['editable'];

    public $with = ['attachments', 'answers', 'subject'];

    const MULTI_CHOICE = 'MULTI_CHOICE';
    const FILLER = 'FILLER';
    const ESSAY = 'ESSAY';

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'teacher_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany('App\Models\Answer');
    }

    public function correctanswer(): HasOne
    {
        return $this->hasOne('App\Models\Answer')->where('is_correct', true);
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'attachable');
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
