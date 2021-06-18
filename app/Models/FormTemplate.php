<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array',
    ];

    public const ACTIVE = 'ACTIVE';
    public const DISABLED = 'DISABLED';
}
