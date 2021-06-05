<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $casts = ['expired_at' => 'datetime'];

    public function transactions(): HasMany
    {
        return $this->hasMany('App\Models\Voucher');
    }
}
