<?php

namespace App\Models;

use App\Trait\Attachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentAssigment extends Model
{
    use HasFactory, Attachable;

    protected $fillable = ["assigment_id", "user_id"];

    protected $with = ['user'];

    public function assigment(): BelongsTo
    {
        return $this->belongsTo("App\Models\Assigment");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function turningAt(Builder $builder, string $turning_at): Builder
    {
        if ($turning_at) {
            return $builder->where('turned_at', $turning_at);
        } else {
            return $builder->whereNull('turned_at');
        }
    }
}
