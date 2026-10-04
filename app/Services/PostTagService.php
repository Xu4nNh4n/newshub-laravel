<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

final class PostTagService
{
    /**
     * Resolve existing IDs and normalized new names to one unique tag ID list.
     *
     * @param  array<int, int|string>  $existingTagIds
     * @param  array<int, mixed>  $newTagNames
     * @return list<int>
     */
    public function resolveIds(array $existingTagIds, array $newTagNames): array
    {
        $resolvedIds = collect($existingTagIds)->map(fn (int|string $id): int => (int) $id);

        $normalizedNames = collect($newTagNames)
            ->filter(fn (mixed $name): bool => is_string($name))
            ->map(function (string $name): array {
                $cleanName = trim(strip_tags($name));

                return ['name' => $cleanName, 'slug' => Str::slug($cleanName)];
            })
            ->filter(fn (array $tag): bool => $tag['name'] !== '' && $tag['slug'] !== '')
            ->unique('slug');

        foreach ($normalizedNames as $normalizedTag) {
            $tag = Tag::query()->createOrFirst(
                ['slug' => $normalizedTag['slug']],
                ['name' => $normalizedTag['name']],
            );
            $resolvedIds->push($tag->id);
        }

        return $resolvedIds->unique()->values()->all();
    }
}
