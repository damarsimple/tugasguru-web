<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quizresult extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "quiz_id", "room_id"];

    protected $with = ['user'];

    public function room(): BelongsTo
    {
        return $this->belongsTo("App\Models\Room");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo("App\Models\Quiz");
    }
}
