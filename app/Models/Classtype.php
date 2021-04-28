<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classtype extends Model
{
    use HasFactory;

    public function schooltype() : BelongsTo
    {
        return $this->belongsTo('App\Models\Schooltype');
    }
}
