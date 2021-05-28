<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $casts = ['ability' => 'object', 'ability_formatted' => 'object'];

    protected $appends = ['ability_alt', 'ability_formatted_alt'];

    public function getAbilityAltAttribute()
    {
        return json_decode($this->ability);
    }

    public function getAbilityFormattedAltAttribute()
    {
        return json_decode($this->ability_formatted);
    }
}
