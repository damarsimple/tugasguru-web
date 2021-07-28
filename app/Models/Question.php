<?php

namespace App\Models;

use App\Trait\Attachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory, Attachable;

    protected $fillable = ["editable"];

    public $with = ["attachments", "answers"];

    public const MULTI_CHOICE = "MULTI_CHOICE";
    public const FILLER = "FILLER";
    public const ESSAY = "ESSAY";
    public const SURVEY = "SURVEY";
    public const SLIDE = "SLIDE";
    public const MANY_ANSWERS = "MANY_ANSWERS";

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function answers(): HasMany
    {
        return $this->hasMany("App\Models\Answer");
    }

    public function correctanswer(): HasOne
    {
        return $this->hasOne("App\Models\Answer")->where("is_correct", true);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo("App\Models\Subject");
    }

    public function classtype(): BelongsTo
    {
        return $this->belongsTo("App\Models\Classtype");
    }
}
