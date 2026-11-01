-- Chon dung schema trong MySQL Workbench truoc khi chay file nay.
-- Dong bo MySQL voi schema gon cho do an; thao tac xoa cot/bang khong the rollback.
-- Hay sao luu database truoc khi chay. Script nay khong xoa bai viet, tai khoan hay binh luan.

SELECT DATABASE() AS database_to_modify;

-- Xoa cac bang con truoc de khong vi pham foreign key.
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `post_views`;
DROP TABLE IF EXISTS `comment_reports`;
DROP TABLE IF EXISTS `post_tag`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `notifications`;

-- Tao bang vai tro va chuyen du lieu users.role sang users.role_id.
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
    (1, 'user', 'Độc giả', NOW(), NOW()),
    (2, 'author', 'Tác giả', NOW(), NOW()),
    (3, 'admin', 'Quản trị viên', NOW(), NOW());

ALTER TABLE `users` ADD COLUMN `role_id` BIGINT UNSIGNED NULL;
UPDATE `users` SET `role_id` = CASE `role`
    WHEN 'author' THEN 2
    WHEN 'admin' THEN 3
    ELSE 1
END;
ALTER TABLE `users`
    MODIFY COLUMN `role_id` BIGINT UNSIGNED NOT NULL DEFAULT 1,
    ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
ALTER TABLE `users` DROP COLUMN `role`, DROP COLUMN `avatar`;

ALTER TABLE `categories` DROP COLUMN `description`;

ALTER TABLE `posts`
    DROP INDEX `posts_is_featured_index`,
    DROP COLUMN `meta_title`,
    DROP COLUMN `meta_description`,
    DROP COLUMN `show_thumbnail_in_post`,
    DROP COLUMN `is_featured`;

ALTER TABLE `comments`
    DROP FOREIGN KEY `comments_reply_to_id_foreign`,
    DROP COLUMN `reply_to_id`;

ALTER TABLE `author_applications`
    ADD COLUMN `cv_path` VARCHAR(255) NULL AFTER `bio`,
    DROP COLUMN `sample_title`,
    DROP COLUMN `sample_content`,
    DROP COLUMN `updated_at`;

ALTER TABLE `post_requests` DROP COLUMN `priority`;

-- Kiem tra lai: ket qua rong neu tat ca cac bang da duoc loai bo.
SELECT `TABLE_NAME`
FROM `INFORMATION_SCHEMA`.`TABLES`
WHERE `TABLE_SCHEMA` = DATABASE()
  AND `TABLE_NAME` IN (
      'favorites',
      'post_views',
      'comment_reports',
      'post_tag',
      'tags',
      'activity_logs'
      ,'notifications'
  )
ORDER BY `TABLE_NAME`;
