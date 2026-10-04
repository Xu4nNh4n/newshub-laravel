<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\DB;

class PostViewRecorder
{
    private const VIEW_TTL_MINUTES = 1440;

    public function __construct(private readonly Repository $cache) {}

    /**
     * Record one view per post and viewer identity during the 24-hour TTL.
     *
     * Authenticated users use their user ID; guests use session ID or IP hash.
     * Returns false when the same identity has already viewed the post recently.
     */
    public function record(Post $post, ?User $viewer, ?string $sessionId, ?string $ipHash): bool
    {
        $identity = $this->viewerIdentity($viewer, $sessionId, $ipHash);

        if ($identity === null) {
            return false;
        }

        $cacheKey = "post-view:{$post->getKey()}:{$identity}";

        if (! $this->cache->add($cacheKey, true, now()->addMinutes(self::VIEW_TTL_MINUTES))) {
            return false;
        }

        DB::transaction(function () use ($post, $viewer, $sessionId, $ipHash): void {
            $post->views()->create([
                'user_id' => $viewer?->getKey(),
                'session_id' => $sessionId,
                'ip_hash' => $ipHash,
                'viewed_at' => now(),
            ]);

            $post->increment('view_count');
        });

        return true;
    }

    private function viewerIdentity(?User $viewer, ?string $sessionId, ?string $ipHash): ?string
    {
        if ($viewer !== null) {
            return 'user:'.$viewer->getKey();
        }

        if (filled($sessionId)) {
            return 'session:'.$sessionId;
        }

        return filled($ipHash) ? 'ip:'.$ipHash : null;
    }
}
