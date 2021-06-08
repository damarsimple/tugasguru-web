<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    use HasFactory;

    const GRADE = "GRADE";

    protected $with = ["user", "users"];

    protected $casts = [
        "data" => "object",
    ];

    protected $appends = ["data_alt"];

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany("App\Models\User");
    }

    public function getDataAltAttribute()
    {
        return json_decode($this->data);
    }
}
