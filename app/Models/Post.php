<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id', 'category_id', 'title', 'meta_title', 'slug', 'summary',
        'meta_description', 'content', 'thumbnail', 'status', 'rejection_reason',
        'show_thumbnail_in_post', 'is_featured', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'show_thumbnail_in_post' => 'boolean',
            'published_at' => 'datetime',
            'status' => PostStatus::class,
        ];
    }

    /**
     * Limit posts to articles currently visible on public pages.
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('category', fn (Builder $query): Builder => $query->where('status', CategoryStatus::Active));
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(PostRequest::class);
    }
}
