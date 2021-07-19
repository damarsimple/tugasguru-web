<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const SEDANG_BERJALAN = 'SEDANG_BERJALAN';
    public const DITOLAK = 'DITOLAK';
    public const SELESAI = 'SELESAI';
    public const DIPERJALANAN = 'DIPERJALANAN';
    public const MENUNGGU = 'MENUNGGU';



    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }



    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', foreignKey: 'teacher_id');
    }
}
