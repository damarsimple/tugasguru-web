<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class QuizReward extends Model
{
    use HasFactory;

    public function transaction(): MorphOne
    {
        return $this->morphOne('App\Models\Transaction', 'transactionable');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo('App\Models\Quiz');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo('App\Models\Reward');
    }
}
