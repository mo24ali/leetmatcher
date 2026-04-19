<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
class Profile extends Model
{
    protected $appends = ['full_avatar_url'];

    protected $fillable = [
        'user_id',
        'avatar_url',
        'bio',
        'education_level',
        'portfolio_url',
        'cv_path',
        'cv_score',
        'availability',
    ];

    /**
     * Get the full URL for the avatar.
     */
    public function getFullAvatarUrlAttribute()
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

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
