<?php

namespace App\Models;

use App\Enums\PostRequestPriority;
use App\Enums\PostRequestStatus;
use App\Enums\PostRequestType;
use Database\Factories\PostRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRequest extends Model
{
    /** @use HasFactory<PostRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'pending_post_id',
        'author_id',
        'type',
        'priority',
        'reason',
        'notes',
        'status',
        'admin_notes',
        'handled_by',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostRequestType::class,
            'priority' => PostRequestPriority::class,
            'status' => PostRequestStatus::class,
            'handled_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
