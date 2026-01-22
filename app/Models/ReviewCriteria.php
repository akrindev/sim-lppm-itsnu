<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewCriteria extends Model
{
    protected $fillable = [
        'type',
        'criteria',
        'description',
        'weight',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ReviewScore::class);
    }
}
