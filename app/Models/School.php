<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    public function teachers(): HasMany
    {
        return $this->hasMany('App\Models\Teacher');
    }
    
    public function partteacher() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Teacher');
    }
}
