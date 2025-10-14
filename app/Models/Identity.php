<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    use HasFactory;



    protected $fillable = [
        'identity_id', // unique lecturer/employee id
        'user_id',
        'address',
        'birthdate',
        'birthplace',
        'profile_picture',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'profile_picture' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
