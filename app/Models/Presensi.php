<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'wisudawan_id',
        'qr_code',
        'waktu_scan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    /**
     * Get the wisudawan that owns this attendance record.
     */
    public function wisudawan(): BelongsTo
    {
        return $this->belongsTo(Wisudawan::class);
    }
}
