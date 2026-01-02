<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kursi extends Model
{
    use HasFactory;

    protected $table = 'kursi';

    protected $fillable = [
        'kode_kursi',
        'section',
        'nomor',
        'hari',
        'wisudawan_id',
        'jenis_kelamin',
    ];

    protected $casts = [
        'nomor' => 'integer',
        'hari' => 'integer',
    ];

    /**
     * Get the wisudawan that occupies this seat.
     */
    public function wisudawan(): BelongsTo
    {
        return $this->belongsTo(Wisudawan::class);
    }

    /**
     * Scope to get seats by section
     */
    public function scopeBySection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope to get seats by hari
     */
    public function scopeByHari($query, int $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope to get empty seats
     */
    public function scopeEmpty($query)
    {
        return $query->whereNull('wisudawan_id');
    }

    /**
     * Scope to get occupied seats
     */
    public function scopeOccupied($query)
    {
        return $query->whereNotNull('wisudawan_id');
    }

    /**
     * Check if seat is occupied
     */
    public function isOccupied(): bool
    {
        return $this->wisudawan_id !== null;
    }
}
