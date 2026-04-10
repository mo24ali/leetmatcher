<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'education_level',
        'portfolio_url',
        'cv_path',
        'cv_score',
        'availability',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the skills associated with the profile.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skills')
                    ->withPivot('proficiency')
                    ->withTimestamps();
    }
}
