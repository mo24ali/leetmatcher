<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    /** @use HasFactory<\Database\Factories\BlogPostFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'body',
        'status',
        'visibility',
        'tags',
        'moderation_status',
        'modification_history',
    ];

    protected $casts = [
        'tags' => 'array',
        'modification_history' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class , 'author_id');
    }
}