<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $appends = ['avatar_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'otp_code',
        'otp_enabled',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'password'          => 'hashed',
            'otp_enabled'       => 'boolean',
            'otp_expires_at'    => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->profile?->full_avatar_url;
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skills', 'profile_id', 'skill_id')
                    ->join('profiles', 'profiles.id', '=', 'profile_skills.profile_id')
                    ->where('profiles.user_id', $this->id)
                    ->withPivot('proficiency')
                    ->withTimestamps();
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }


    public function applications(){
        return $this->hasMany(Application::class, 'student_id');
    }

    // admin only
    public function moderationActions()
    {
        return $this->hasMany(ModerationAction::class);
    }
}
