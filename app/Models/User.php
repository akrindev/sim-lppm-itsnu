<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The type of the auto-incrementing ID's primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Get the identity associated with the user.
     */
    public function identity(): HasOne
    {
        return $this->hasOne(Identity::class, 'user_id');
    }

    /**
     * Get all proposals submitted by the user.
     */
    public function submittedProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'submitter_id');
    }

    /**
     * Get all proposals where the user is a team member.
     */
    public function proposals(): BelongsToMany
    {
        return $this->belongsToMany(Proposal::class, 'proposal_user')
            ->withPivot('role', 'tasks')
            ->withTimestamps();
    }

    /**
     * Get all research stages where the user is the person in charge.
     */
    public function researchStages(): HasMany
    {
        return $this->hasMany(ResearchStage::class, 'person_in_charge_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factory_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // attributes
    public function profilePicture(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->identity?->profile_picture
                ?? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s=128&d=identicon',
        );
    }
}
