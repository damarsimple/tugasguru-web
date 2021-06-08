<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Meeting extends Model
{
    use HasFactory;

    protected $with = [
        "rooms",
        "classroom.students",
        "subject",
        "teacher",
        "article",
    ];

    public $appends = ["absents"];

    protected $casts = ["data" => "object", "content" => "object"];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo("App\Models\Classroom");
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo("App\Models\Article");
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo("App\Models\Subject");
    }

    public function getAbsentsAttribute()
    {
        $builder = $this->teacher
            ->studentabsents()
            ->whereHas(
                "user.myclassrooms",
                fn($e) => $e->where("classroom_id", $this->classroom_id)
            )
            ->whereDate("start_at", Carbon::today());

        if ($this->finish_at) {
            $builder->whereDate("finish_at", $this->finish_at);
        }

        return $builder->get();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo("App\Models\User", "teacher_id");
    }

    public function rooms(): MorphMany
    {
        return $this->morphMany("App\Models\Room", "roomable");
    }

    public function attachments()
    {
        return $this->morphMany("App\Models\Attachment", "attachable");
    }

    public function attendances()
    {
        return $this->morphMany("App\Models\Attachment", "attendable");
    }
}
