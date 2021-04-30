<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Answer extends Model
{
    use HasFactory;
    
    public function question() : BelongsTo
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function attachment() : MorphOne
    {
        return $this->morphOne('App\Models\Attachment', 'attachable');
    }
    
}
