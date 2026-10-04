# ERD — Hệ thống quản lý và cung cấp tin tức

Sơ đồ dưới đây mô tả các bảng nghiệp vụ chính. Các bảng mặc định của Laravel như `sessions`, `cache`, `jobs` và `password_reset_tokens` không được vẽ để sơ đồ tập trung vào miền tin tức.

```mermaid
erDiagram
    USERS ||--o{ POSTS : writes
    USERS ||--o{ COMMENTS : creates
    USERS ||--o{ FAVORITES : saves
    USERS ||--o{ COMMENT_REPORTS : reports
    USERS ||--o{ COMMENT_REPORTS : handles
    USERS ||--o{ POST_VIEWS : views
    USERS ||--o{ ACTIVITY_LOGS : performs
    CATEGORIES ||--o{ POSTS : contains
    POSTS ||--o{ COMMENTS : has
    POSTS ||--o{ FAVORITES : saved_as
    POSTS ||--o{ POST_VIEWS : receives
    POSTS }o--o{ TAGS : tagged_with
    COMMENTS ||--o{ COMMENTS : parent_reply
    COMMENTS ||--o{ COMMENTS : direct_reply
    COMMENTS ||--o{ COMMENT_REPORTS : receives

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string avatar
        string role
        string status
        timestamp email_verified_at
    }
    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        text description
        string status
        timestamp deleted_at
    }
    TAGS {
        bigint id PK
        string name
        string slug UK
    }
    POSTS {
        bigint id PK
        bigint author_id FK
        bigint category_id FK
        string title
        string slug UK
        text summary
        longtext content
        string thumbnail
        string status
        text rejection_reason
        boolean is_featured
        timestamp published_at
        bigint view_count
        timestamp deleted_at
    }
    POST_TAG {
        bigint post_id PK, FK
        bigint tag_id PK, FK
    }
    COMMENTS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        bigint parent_id FK
        bigint reply_to_id FK
        text content
        string status
        timestamp deleted_at
    }
    COMMENT_REPORTS {
        bigint id PK
        bigint comment_id FK
        bigint reporter_id FK
        string reason
        text description
        string status
        bigint handled_by FK
        timestamp handled_at
    }
    FAVORITES {
        bigint id PK
        bigint user_id FK
        bigint post_id FK
        timestamp created_at
    }
    POST_VIEWS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        string session_id
        string ip_hash
        timestamp viewed_at
    }
    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string subject_type
        bigint subject_id
        text description
        timestamp created_at
    }
```

## Quy tắc quan trọng

- `posts.status`: `draft`, `pending_review`, `published`, `rejected`, `hidden`, `archived`.
- Chỉ bài `published` có `published_at` không lớn hơn thời điểm hiện tại mới xuất hiện công khai.
- `comments.parent_id` luôn trỏ về bình luận gốc; `reply_to_id` trỏ về bình luận được trả lời trực tiếp.
- `favorites` và `post_tag` có khóa duy nhất để tránh dữ liệu trùng.
- `comment_reports` giới hạn mỗi người dùng chỉ báo cáo một bình luận một lần.
- Các bảng `categories`, `posts`, `comments` dùng soft delete để không phá vỡ lịch sử và hội thoại.
