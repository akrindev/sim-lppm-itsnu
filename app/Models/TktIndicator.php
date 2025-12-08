<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TktIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'tkt_level_id',
        'code',
        'indicator',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(TktLevel::class, 'tkt_level_id');
    }
}
