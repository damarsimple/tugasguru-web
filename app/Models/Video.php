<?php

namespace App\Models;

use App\Enum\Constant;
use App\Trait\Attachable;
use App\Trait\Sociable;
use App\Trait\Thumbnailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Video extends Model
{
    use HasFactory, Attachable, Sociable, Thumbnailable;

    public function file(): MorphOne
    {
        return $this->morphOne("App\Models\Attachment", "attachable")->where('role', Constant::VIDEO);
    }
}
