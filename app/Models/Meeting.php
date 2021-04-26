<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Meeting extends Model
{
    use HasFactory;

    public function classroom(): BelongsTo
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function article() : BelongsTo
    {
        return $this->belongsTo('App\Models\Article');
    }

    public function subject() : BelongsTo
    {
        return $this->belongsTo('App\Models\Subject');
    }
    
}
