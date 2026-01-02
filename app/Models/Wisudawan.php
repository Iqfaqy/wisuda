<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wisudawan extends Model
{
    use HasFactory;

    protected $table = 'wisudawan';

    protected $fillable = [
        'user_id',
        'prodi',
        'fakultas',
        'ipk',
        'predikat',
        'jenis_kelamin',
        'telepon',
        'nama_ortu',
        'jumlah_tamu',
        'hari_wisuda',
        'judul_skripsi',
        'nama_ayah',
        'nama_ibu',
        'ukuran_toga',
    ];

    protected $casts = [
        'ipk' => 'decimal:2',
        'jumlah_tamu' => 'integer',
        'hari_wisuda' => 'integer',
    ];

    /**
     * Get the user that owns the wisudawan profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the seat assigned to this wisudawan.
     */
    public function kursi(): HasOne
    {
        return $this->hasOne(Kursi::class);
    }

    /**
     * Get the attendance record for this wisudawan.
     */
    public function presensi(): HasOne
    {
        return $this->hasOne(Presensi::class);
    }

    /**
     * Calculate predikat based on IPK
     */
    public function calculatePredikat(): string
    {
        if ($this->ipk >= 3.76) return 'Cumlaude';
        if ($this->ipk >= 3.51) return 'Sangat Memuaskan';
        if ($this->ipk >= 2.76) return 'Memuaskan';
        return 'Cukup';
    }
}
