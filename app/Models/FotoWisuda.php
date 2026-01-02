<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoWisuda extends Model
{
    use HasFactory;

    protected $table = 'foto_wisuda';

    protected $fillable = [
        'drive_link',
        'hari',
        'deskripsi',
        'created_by',
    ];

    /**
     * Get the admin user who created this photo entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
