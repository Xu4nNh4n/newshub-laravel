<?php

namespace App\Models;

use App\Enums\CommentReportReason;
use App\Enums\CommentReportStatus;
use Database\Factories\CommentReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    /** @use HasFactory<CommentReportFactory> */
    use HasFactory;

    protected $fillable = ['comment_id', 'reporter_id', 'reason', 'description', 'status', 'handled_by', 'handled_at'];

    protected function casts(): array
    {
        return [
            'reason' => CommentReportReason::class,
            'status' => CommentReportStatus::class,
            'handled_at' => 'datetime',
        ];
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
