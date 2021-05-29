<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $casts = ['ability' => 'array'];

    protected $appends = ['ability_alt'];

    public function getAbilityAltAttribute()
    {
        return json_decode($this->ability);
    }
}
