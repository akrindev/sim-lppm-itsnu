<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'institution',
        'country',
        'type',
        'address',
        'commitment_letter_file',
    ];

    /**
     * Get all community services associated with the partner.
     */
    public function communityServices(): HasMany
    {
        return $this->hasMany(CommunityService::class);
    }

    /**
     * Get all proposals associated with this partner.
     */
    public function proposals(): BelongsToMany
    {
        return $this->belongsToMany(Proposal::class);
    }
}
