<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    public function messages()
    {
        return $this->morphMany('App\Models\Message', 'messageable');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User')->withPivot('is_administrator');
    }
}
