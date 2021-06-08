<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    public const BALANCE = "BALANCE";
    public const XENDIT = "XENDIT";
    public const ADMIN = "ADMIN";

    public const STAGING = "STAGING";
    public const PENDING = "PENDING";
    public const SUCCESS = "SUCCESS";
    public const FAILED = "FAILED";

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
