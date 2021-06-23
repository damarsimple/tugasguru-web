<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPpdb extends Model
{
    use HasFactory;

    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';
    public const PROCESSED = 'PROCESSED';
    public const PENDING = 'PENDING';

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }


    public function form(): BelongsTo
    {
        return $this->belongsTo('App\Models\Form');
    }

    public function wave(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wave');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo('App\Models\School');
    }

    public function getIsPaidAttribute(): bool
    {

        if (!$this->wave_id) return false;

        return Transaction::where([
            'transactionable_id' => $this->wave_id,
            'transactionable_type' => Wave::class,
            'user_id' => $this->user_id,
            'is_paid' => true,
        ])->exists();
    }
}
