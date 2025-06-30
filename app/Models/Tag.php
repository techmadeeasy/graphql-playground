<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    // Relationships
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->published();
    }

    // Accessors
    public function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getPublishedPostCountAttribute(): int
    {
        return $this->publishedPosts()->count();
    }
}
