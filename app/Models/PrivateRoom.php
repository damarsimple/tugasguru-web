<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PrivateRoom extends Model
{
    use HasFactory;

    protected $fillable = ["first_id", "second_id"];

    protected $with = ["firstmessage", "first", "second"];

    public function messages(): MorphMany
    {
        return $this->morphMany("App\Models\Message", "messageable");
    }

    public function firstmessage(): MorphOne
    {
        return $this->morphOne("App\Models\Message", "messageable")->latest();
    }

    public function first(): BelongsTo
    {
        return $this->belongsTo("App\Models\User", "first_id");
    }

    public function second(): BelongsTo
    {
        return $this->belongsTo("App\Models\User", "second_id");
    }
}
