<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $appends = ["path"];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function getPathAttribute()
    {
        return env("APP_URL", "http://localhost") .
            "/attachments/" .
            $this->name;
    }

    public function getFilePathAttribute()
    {
        return public_path("attachments") . "/" . $this->name;
    }

    public function getTempFilePathAttribute()
    {
        return public_path("attachments") . "/temp." . $this->name;
    }

    public function getExtAttribute()
    {
        try {
            return explode(".", $this->name)[1];
        } catch (\Throwable $th) {
            return null;
        }
    }
}
