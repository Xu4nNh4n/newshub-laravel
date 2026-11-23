<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'name', 'slug', 'status'];

    protected function casts(): array
    {
        return ['status' => CategoryStatus::class];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Limit categories to active ones whose parent category is not hidden. */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', CategoryStatus::Active)
            ->whereDoesntHave('parent', fn (Builder $query): Builder => $query->where('status', CategoryStatus::Hidden));
    }

    /** @return list<int> */
    public function selfAndChildIds(): array
    {
        $this->loadMissing('children:id,parent_id');

        return [$this->id, ...$this->children->modelKeys()];
    }
}
