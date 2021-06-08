<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absent extends Model
{
    use HasFactory;

    public $with = ["user", "teacher"];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo("App\Models\User", "teacher_id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }
}
