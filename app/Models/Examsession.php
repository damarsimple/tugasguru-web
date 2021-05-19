<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examsession extends Model
{
    use HasFactory;

    protected $hidden = ['token'];

    public $with = ['exam.teacher'];

    protected $casts = [
        'open_at' => 'datetime',
        'close_at' => 'datetime'
    ];
    public function exam(): BelongsTo
    {
        return $this->belongsTo('App\Models\Exam');
    }

}
