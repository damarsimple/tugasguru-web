<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "school_id",
        "agenda_id"
    ];

    protected $with = ["user", "attendable"];

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }


    public function school(): BelongsTo
    {
        return $this->belongsTo("App\Models\School");
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo('App\Models\Agenda');
    }
}
