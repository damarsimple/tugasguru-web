<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    public function student(): BelongsTo
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\Teacher');
    }
}
