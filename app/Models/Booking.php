<?php

namespace App\Models;

use App\Trait\Transactionable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Booking extends Model
{
    use HasFactory, Transactionable;

    public const SEDANG_BERJALAN = 'SEDANG_BERJALAN';
    public const DITOLAK = 'DITOLAK';
    public const SELESAI = 'SELESAI';
    public const DIPERJALANAN = 'DIPERJALANAN';
    public const MENUNGGU = 'MENUNGGU';

    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
        'coordinate' => 'array',
        'address_detail' => 'array',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }



    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', foreignKey: 'teacher_id');
    }

    public function agenda(): MorphOne
    {
        return $this->morphOne("App\Models\Agenda", "agendaable");
    }
}
