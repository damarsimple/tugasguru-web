<?php

namespace App\Models;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Form extends Model
{
    use HasFactory;
    public const REQUEST_TUTOR = "REQUEST_TUTOR";
    public const REQUEST_COUNSELOR = "REQUEST_COUNSELOR";
    public const REQUEST_HEADMASTER = "REQUEST_HEADMASTER";
    public const REQUEST_ADMIN_SCHOOL = "REQUEST_ADMIN_SCHOOL";
    public const REQUEST_HOMEROOM = "REQUEST_HOMEROOM";
    public const REQUEST_STUDENT_PPDB = "REQUEST_STUDENT_PPDB";

    public const PENDING = 0;
    public const PROCESSED = 1;
    public const FINISHED = 2;
    public const REJECTED = 3;

    public const DOCUMENTS  = 'DOCUMENTS';

    protected $casts = [
        "data" => "object",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo("App\Models\User");
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo("App\Models\School");
    }

    public function wave(): BelongsTo
    {
        return $this->belongsTo("App\Models\Wave");
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany("App\Models\Attachment", "attachable");
    }

    public function studentppdb(): HasOne
    {
        return $this->hasOne("App\Models\StudentPpdb");
    }

    public function ppdbforms($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): Builder
    {
        return Form::where('school_id', request()?->user()?->admin_school_id)->where('is_ppdb', true);
    }
}
