<?php

namespace App\Enums;

enum CommentReportReason: string
{
    case Spam = 'spam';
    case Abusive = 'abusive';
    case Misinformation = 'misinformation';
    case Irrelevant = 'irrelevant';
    case Dangerous = 'dangerous';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Spam => 'Spam hoặc quảng cáo',
            self::Abusive => 'Xúc phạm hoặc quấy rối',
            self::Misinformation => 'Thông tin sai lệch',
            self::Irrelevant => 'Không liên quan',
            self::Dangerous => 'Nội dung nguy hiểm',
            self::Other => 'Lý do khác',
        };
    }
}
