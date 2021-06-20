<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Form extends Model
{
    public const REQUEST_TUTOR = "REQUEST_TUTOR";
    public const REQUEST_COUNSELOR = "REQUEST_COUNSELOR";
    public const REQUEST_HEADMASTER = "REQUEST_HEADMASTER";
    public const REQUEST_ADMIN_SCHOOL = "REQUEST_ADMIN_SCHOOL";
    public const REQUEST_HOMEROOM = "REQUEST_HOMEROOM";

    public const PENDING = 0;
    public const PROCESSED = 1;
    public const FINISHED = 2;
    public const REJECTED = 3;

    public const DOCUMENTS  = 'DOCUMENTS';

    use HasFactory;

    protected $casts = [
        "data" => "object",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }
}
