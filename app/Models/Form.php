<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Form extends Model
{

    const REQUEST_TUTOR  = 'REQUEST_TUTOR';
    const REQUEST_COUNSELOR  = 'REQUEST_COUNSELOR';
    const REQUEST_HEADMASTER  = 'REQUEST_HEADMASTER';
    const REQUEST_PPDB  = 'REQUEST_PPDB';
    const REQUEST_HOMEROOM  = 'REQUEST_HOMEROOM';


    const PENDING = 0;
    const PROCESSED = 1;
    const FINISHED = 2;
    const REJECTED = 3;

    use HasFactory;

    protected $casts = [
        'data' => 'object'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }
}
