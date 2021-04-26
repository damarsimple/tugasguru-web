<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $appends = ['path'];

    public function getPathAttribute(): string|null
    {
        return $this->name;
    }
}
