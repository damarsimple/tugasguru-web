<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Autosave extends Model
{
    use HasFactory;

    public const QUESTION_EDITOR = 'QUESTION_EDITOR';
    public const EXAM_EDITOR = 'EXAM_EDITOR';

    protected $casts = [
        "data" => "object",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }
}
