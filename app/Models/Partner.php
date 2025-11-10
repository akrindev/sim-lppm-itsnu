<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Partner extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory, InteractsWithMedia;

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

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('commitment_letter')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg']);
    }
}
