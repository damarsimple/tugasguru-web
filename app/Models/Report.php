<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    const GRADE = 'GRADE';

    protected $with = ['user', 'to'];

    protected $casts = [
        'data' => 'object',
    ];

    protected $appends = [
        'data_alt',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getDataAltAttribute()
    {
        return json_decode($this->data);
    }
}
