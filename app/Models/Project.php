<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'recruiter_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(User::class , 'recruiter_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skills')
                    ->withPivot('level')
                    ->withTimestamps();
    }
}