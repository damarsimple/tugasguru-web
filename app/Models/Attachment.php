<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $appends = ['path'];

    public function attachable()
    {
        return $this->morphTo();
    }

    function getPathAttribute()
    {
        return request()->getSchemeAndHttpHost() . '/attachments/' . $this->name;
    }

    public function getFilePathAttribute()
    {
        
    }
}
