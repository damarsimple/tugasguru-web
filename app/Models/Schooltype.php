<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schooltype extends Model
{
    use HasFactory;

    public function classtypes() : HasMany
    {
        return $this->hasMany('App\Models\Classtype');
    }
}
