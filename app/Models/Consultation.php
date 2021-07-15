<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo("App\Models\User", "consultant_id");
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo('App\Models\Consultation');
    }
}
