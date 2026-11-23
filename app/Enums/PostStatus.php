<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Published = 'published';
    case Rejected = 'rejected';
    case Hidden = 'hidden';
    case Archived = 'archived';

    /** Return the Vietnamese label shown in the interface. */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bản nháp',
            self::PendingReview => 'Chờ duyệt',
            self::Published => 'Đã xuất bản',
            self::Rejected => 'Bị từ chối',
            self::Hidden => 'Đã tạm ẩn',
            self::Archived => 'Đã lưu trữ',
        };
    }
}
