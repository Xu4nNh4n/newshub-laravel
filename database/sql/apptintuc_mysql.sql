-- ============================================================================
-- NewsHub - CSDL MySQL Chuẩn Cho Dự Án Website Tin Tức & Đồ Án
-- Tương thích: MySQL 8.x / MariaDB 10.x / Laragon / XAMPP / phpMyAdmin
-- Ngày tạo: 2026-10-04 15:19:57
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40014 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- 1. Tạo Database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `apptintuc` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `apptintuc`;

-- ============================================================================
-- 2. XÓA CÁC BẢNG CŨ (NẾU CÓ - AN TOÀN KHI IMPORT LẠI)
-- ============================================================================

DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `post_requests`;
DROP TABLE IF EXISTS `author_applications`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `post_views`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `comment_reports`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `post_tag`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;


-- ============================================================================
-- 3. CẤU TRÚC BẢNG (DDL)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: 0001_01_01_000000_create_users_table.php
create table `users` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `email` varchar(255) not null, `email_verified_at` timestamp null, `password` varchar(255) not null, `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000000_create_users_table.php
alter table `users` add unique `users_email_unique`(`email`);

-- Migration: 0001_01_01_000000_create_users_table.php
create table `password_reset_tokens` (`email` varchar(255) not null, `token` varchar(255) not null, `created_at` timestamp null, primary key (`email`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000000_create_users_table.php
create table `sessions` (`id` varchar(255) not null, `user_id` bigint unsigned null, `ip_address` varchar(45) null, `user_agent` text null, `payload` longtext not null, `last_activity` int not null, primary key (`id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000000_create_users_table.php
alter table `sessions` add index `sessions_user_id_index`(`user_id`);

-- Migration: 0001_01_01_000000_create_users_table.php
alter table `sessions` add index `sessions_last_activity_index`(`last_activity`);

-- Migration: 0001_01_01_000001_create_cache_table.php
create table `cache` (`key` varchar(255) not null, `value` mediumtext not null, `expiration` bigint not null, primary key (`key`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000001_create_cache_table.php
alter table `cache` add index `cache_expiration_index`(`expiration`);

-- Migration: 0001_01_01_000001_create_cache_table.php
create table `cache_locks` (`key` varchar(255) not null, `owner` varchar(255) not null, `expiration` bigint not null, primary key (`key`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000001_create_cache_table.php
alter table `cache_locks` add index `cache_locks_expiration_index`(`expiration`);

-- Migration: 0001_01_01_000002_create_jobs_table.php
create table `jobs` (`id` bigint unsigned not null auto_increment primary key, `queue` varchar(255) not null, `payload` longtext not null, `attempts` smallint unsigned not null, `reserved_at` int unsigned null, `available_at` int unsigned not null, `created_at` int unsigned not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000002_create_jobs_table.php
alter table `jobs` add index `jobs_queue_index`(`queue`);

-- Migration: 0001_01_01_000002_create_jobs_table.php
create table `job_batches` (`id` varchar(255) not null, `name` varchar(255) not null, `total_jobs` int not null, `pending_jobs` int not null, `failed_jobs` int not null, `failed_job_ids` longtext not null, `options` mediumtext null, `cancelled_at` int null, `created_at` int not null, `finished_at` int null, primary key (`id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000002_create_jobs_table.php
create table `failed_jobs` (`id` bigint unsigned not null auto_increment primary key, `uuid` varchar(255) not null, `connection` varchar(255) not null, `queue` varchar(255) not null, `payload` longtext not null, `exception` longtext not null, `failed_at` timestamp not null default CURRENT_TIMESTAMP) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 0001_01_01_000002_create_jobs_table.php
alter table `failed_jobs` add index `failed_jobs_connection_queue_failed_at_index`(`connection`, `queue`, `failed_at`);

-- Migration: 0001_01_01_000002_create_jobs_table.php
alter table `failed_jobs` add unique `failed_jobs_uuid_unique`(`uuid`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `users` add `avatar` varchar(255) null after `password`;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `users` add `role` varchar(255) not null default 'user' after `avatar`;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `users` add `status` varchar(255) not null default 'active' after `role`;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `users` add index `users_role_index`(`role`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `users` add index `users_status_index`(`status`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `categories` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `slug` varchar(255) not null, `description` text null, `status` varchar(255) not null default 'active', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `categories` add unique `categories_slug_unique`(`slug`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `categories` add index `categories_status_index`(`status`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `tags` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `slug` varchar(255) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `tags` add unique `tags_slug_unique`(`slug`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `posts` (`id` bigint unsigned not null auto_increment primary key, `author_id` bigint unsigned not null, `category_id` bigint unsigned not null, `title` varchar(255) not null, `slug` varchar(255) not null, `summary` text null, `content` longtext not null, `thumbnail` varchar(255) null, `status` varchar(255) not null default 'draft', `rejection_reason` text null, `is_featured` tinyint(1) not null default '0', `published_at` timestamp null, `view_count` bigint unsigned not null default '0', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add constraint `posts_author_id_foreign` foreign key (`author_id`) references `users` (`id`) on delete restrict;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add constraint `posts_category_id_foreign` foreign key (`category_id`) references `categories` (`id`) on delete restrict;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add index `posts_status_published_at_index`(`status`, `published_at`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add unique `posts_slug_unique`(`slug`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add index `posts_status_index`(`status`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add index `posts_is_featured_index`(`is_featured`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `posts` add index `posts_published_at_index`(`published_at`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `post_tag` (`post_id` bigint unsigned not null, `tag_id` bigint unsigned not null, primary key (`post_id`, `tag_id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_tag` add constraint `post_tag_post_id_foreign` foreign key (`post_id`) references `posts` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_tag` add constraint `post_tag_tag_id_foreign` foreign key (`tag_id`) references `tags` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `comments` (`id` bigint unsigned not null auto_increment primary key, `post_id` bigint unsigned not null, `user_id` bigint unsigned not null, `parent_id` bigint unsigned null, `reply_to_id` bigint unsigned null, `content` text not null, `status` varchar(255) not null default 'visible', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add constraint `comments_post_id_foreign` foreign key (`post_id`) references `posts` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add constraint `comments_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add constraint `comments_parent_id_foreign` foreign key (`parent_id`) references `comments` (`id`) on delete set null;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add constraint `comments_reply_to_id_foreign` foreign key (`reply_to_id`) references `comments` (`id`) on delete set null;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add index `comments_post_id_parent_id_created_at_index`(`post_id`, `parent_id`, `created_at`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comments` add index `comments_status_index`(`status`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `comment_reports` (`id` bigint unsigned not null auto_increment primary key, `comment_id` bigint unsigned not null, `reporter_id` bigint unsigned not null, `reason` varchar(255) not null, `description` text null, `status` varchar(255) not null default 'pending', `handled_by` bigint unsigned null, `handled_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comment_reports` add constraint `comment_reports_comment_id_foreign` foreign key (`comment_id`) references `comments` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comment_reports` add constraint `comment_reports_reporter_id_foreign` foreign key (`reporter_id`) references `users` (`id`) on delete restrict;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comment_reports` add constraint `comment_reports_handled_by_foreign` foreign key (`handled_by`) references `users` (`id`) on delete set null;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comment_reports` add unique `comment_reports_comment_id_reporter_id_unique`(`comment_id`, `reporter_id`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `comment_reports` add index `comment_reports_status_index`(`status`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `favorites` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `post_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `favorites` add constraint `favorites_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `favorites` add constraint `favorites_post_id_foreign` foreign key (`post_id`) references `posts` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `favorites` add unique `favorites_user_id_post_id_unique`(`user_id`, `post_id`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `post_views` (`id` bigint unsigned not null auto_increment primary key, `post_id` bigint unsigned not null, `user_id` bigint unsigned null, `session_id` varchar(255) null, `ip_hash` varchar(64) null, `viewed_at` timestamp not null default CURRENT_TIMESTAMP) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_views` add constraint `post_views_post_id_foreign` foreign key (`post_id`) references `posts` (`id`) on delete cascade;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_views` add constraint `post_views_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete set null;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_views` add index `post_views_session_id_index`(`session_id`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_views` add index `post_views_ip_hash_index`(`ip_hash`);

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `post_views` add index `post_views_viewed_at_index`(`viewed_at`);

-- Migration: 2026_09_27_000000_create_news_schema.php
create table `activity_logs` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned null, `action` varchar(255) not null, `subject_type` varchar(255) null, `subject_id` bigint unsigned null, `description` text null, `created_at` timestamp not null default CURRENT_TIMESTAMP) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `activity_logs` add constraint `activity_logs_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete set null;

-- Migration: 2026_09_27_000000_create_news_schema.php
alter table `activity_logs` add index `activity_logs_subject_type_subject_id_index`(`subject_type`, `subject_id`);

-- Migration: 2026_09_27_081033_add_seo_fields_to_posts_table.php
alter table `posts` add `meta_title` varchar(255) null after `title`;

-- Migration: 2026_09_27_081033_add_seo_fields_to_posts_table.php
alter table `posts` add `meta_description` varchar(500) null after `summary`;

-- Migration: 2026_09_27_143147_add_reporting_indexes_to_news_tables.php
alter table `posts` add index `posts_view_count_index`(`view_count`);

-- Migration: 2026_09_27_143147_add_reporting_indexes_to_news_tables.php
alter table `activity_logs` add index `activity_logs_action_created_at_index`(`action`, `created_at`);

-- Migration: 2026_09_27_143147_add_reporting_indexes_to_news_tables.php
alter table `activity_logs` add index `activity_logs_user_created_at_index`(`user_id`, `created_at`);

-- Migration: 2026_10_03_184025_create_author_applications_table.php
create table `author_applications` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `pending_user_id` bigint unsigned null, `category_id` bigint unsigned not null, `bio` text not null, `sample_title` varchar(255) not null, `sample_content` text not null, `status` varchar(255) not null default 'pending', `rejection_reason` varchar(255) null, `reviewed_by` bigint unsigned null, `reviewed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_10_03_184025_create_author_applications_table.php
alter table `author_applications` add constraint `author_applications_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

-- Migration: 2026_10_03_184025_create_author_applications_table.php
alter table `author_applications` add constraint `author_applications_category_id_foreign` foreign key (`category_id`) references `categories` (`id`) on delete cascade;

-- Migration: 2026_10_03_184025_create_author_applications_table.php
alter table `author_applications` add constraint `author_applications_reviewed_by_foreign` foreign key (`reviewed_by`) references `users` (`id`) on delete set null;

-- Migration: 2026_10_03_184025_create_author_applications_table.php
alter table `author_applications` add index `author_applications_status_created_at_index`(`status`, `created_at`);

-- Migration: 2026_10_03_184025_create_author_applications_table.php
alter table `author_applications` add unique `author_applications_pending_user_id_unique`(`pending_user_id`);

-- Migration: 2026_10_04_040548_add_parent_id_to_categories_table.php
alter table `categories` add `parent_id` bigint unsigned null after `id`;

-- Migration: 2026_10_04_040548_add_parent_id_to_categories_table.php
alter table `categories` add constraint `categories_parent_id_foreign` foreign key (`parent_id`) references `categories` (`id`) on delete set null;

-- Migration: 2026_10_04_063300_add_show_thumbnail_in_post_to_posts_table.php
alter table `posts` add `show_thumbnail_in_post` tinyint(1) not null default '1' after `thumbnail`;

-- Migration: 2026_10_04_095351_create_post_requests_table.php
create table `post_requests` (`id` bigint unsigned not null auto_increment primary key, `post_id` bigint unsigned not null, `pending_post_id` bigint unsigned null, `author_id` bigint unsigned not null, `type` varchar(255) not null, `priority` varchar(255) not null default 'normal', `reason` varchar(255) not null, `notes` text not null, `status` varchar(255) not null default 'pending', `admin_notes` text null, `handled_by` bigint unsigned null, `handled_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add constraint `post_requests_post_id_foreign` foreign key (`post_id`) references `posts` (`id`) on delete cascade;

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add constraint `post_requests_author_id_foreign` foreign key (`author_id`) references `users` (`id`) on delete cascade;

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add constraint `post_requests_handled_by_foreign` foreign key (`handled_by`) references `users` (`id`) on delete set null;

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add index `post_requests_status_created_at_index`(`status`, `created_at`);

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add index `post_requests_author_id_created_at_index`(`author_id`, `created_at`);

-- Migration: 2026_10_04_095351_create_post_requests_table.php
alter table `post_requests` add unique `post_requests_pending_post_id_unique`(`pending_post_id`);

-- Migration: 2026_10_04_140431_create_notifications_table.php
create table `notifications` (`id` char(36) not null, `type` varchar(255) not null, `notifiable_type` varchar(255) not null, `notifiable_id` bigint unsigned not null, `data` text not null, `read_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci';

-- Migration: 2026_10_04_140431_create_notifications_table.php
alter table `notifications` add index `notifications_notifiable_type_notifiable_id_index`(`notifiable_type`, `notifiable_id`);

-- ============================================================================
-- 4. DỮ LIỆU MẪU ĐẦY ĐỦ (DATA INSERTS)
-- ============================================================================

-- Dữ liệu cho bảng `migrations` (11 dòng)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_27_000000_create_news_schema', 1),
(5, '2026_09_27_081033_add_seo_fields_to_posts_table', 2),
(6, '2026_09_27_143147_add_reporting_indexes_to_news_tables', 3),
(7, '2026_10_03_184025_create_author_applications_table', 4),
(8, '2026_10_04_040548_add_parent_id_to_categories_table', 5),
(9, '2026_10_04_063300_add_show_thumbnail_in_post_to_posts_table', 6),
(10, '2026_10_04_095351_create_post_requests_table', 7),
(11, '2026_10_04_140431_create_notifications_table', 8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dữ liệu cho bảng `users` (4 dòng)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `avatar`, `role`, `status`) VALUES
(1, 'Xuân Nhân', 'bbhhhfgfg@gmail.com', '2026-10-03 16:43:12', '$2y$12$SWHDEeJ/062VI9XPaYFmI.wmLr/7XeG73IOskN9.ww1i1KaxjCJ6a', NULL, '2026-10-03 16:42:27', '2026-10-03 16:43:12', NULL, 'user', 'active'),
(2, 'Quản trị viên Demo', 'admin@newshub.test', '2026-10-04 04:13:03', '$2y$12$EcqcJkPT2ytVQX4UJM/F7et.Nh39nInunPdtXvXWZM8tw.bxegHbq', 'yFEa6pVDuQjUaKdfbV3TznSMMUGV2J13dwQA9zE2kMtEcfTZcyznjdEJFfsE', '2026-10-03 16:46:26', '2026-10-04 04:13:03', NULL, 'admin', 'active'),
(3, 'Tác giả Demo', 'author@newshub.test', '2026-10-04 04:13:04', '$2y$12$nJVf2sBhYihtGecAzYytr.JL8jJqM3gcGHUNG5rDi2WMUteAV/VcC', NULL, '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 'author', 'active'),
(4, 'Độc giả Demo', 'reader@newshub.test', '2026-10-04 04:13:04', '$2y$12$tJUWPcvL828lNrkglWHaLOLSU0sd7XbApnvr71DTzCM2lvH/KqgEO', NULL, '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 'user', 'active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dữ liệu cho bảng `categories` (11 dòng)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`, `parent_id`) VALUES
(1, 'Công nghệ', 'cong-nghe', 'Tin tức Công nghệ', 'active', '2026-10-03 16:46:26', '2026-10-03 16:46:26', NULL, NULL),
(2, 'Trí tuệ nhân tạo', 'tri-tue-nhan-tao', 'Tin tức Trí tuệ nhân tạo', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(3, 'An ninh mạng', 'an-ninh-mang', 'Tin tức An ninh mạng', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(4, 'Phần mềm', 'phan-mem', 'Tin tức Phần mềm', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(5, 'Phần cứng', 'phan-cung', 'Tin tức Phần cứng', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(6, 'Điện toán đám mây', 'dien-toan-dam-may', 'Tin tức Điện toán đám mây', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(7, 'Dữ liệu', 'du-lieu', 'Tin tức Dữ liệu', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(8, 'Khởi nghiệp công nghệ', 'khoi-nghiep-cong-nghe', 'Tin tức Khởi nghiệp công nghệ', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 10),
(9, 'Chuyển đổi số', 'chuyen-doi-so', 'Tin tức Chuyển đổi số', 'active', '2026-10-03 16:46:26', '2026-10-04 04:13:04', NULL, 1),
(10, 'Kinh doanh', 'kinh-doanh', 'Tin tức Kinh doanh', 'active', '2026-10-03 16:46:26', '2026-10-03 16:46:26', NULL, NULL),
(11, 'Đời sống', 'doi-song', 'Tin tức Đời sống', 'active', '2026-10-03 16:46:26', '2026-10-03 16:46:26', NULL, NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Dữ liệu cho bảng `tags` (12 dòng)
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Laravel', 'laravel', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(2, 'PHP', 'php', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(3, 'AI', 'ai', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(4, 'Startup', 'startup', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(5, 'Cybersecurity', 'cybersecurity', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(6, 'Cloud', 'cloud', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(7, 'DevOps', 'devops', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(8, 'Open Source', 'open-source', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(9, 'Dữ liệu lớn', 'du-lieu-lon', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(10, 'Điện thoại thông minh', 'dien-thoai-thong-minh', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(11, 'IoT', 'iot', '2026-10-03 16:46:26', '2026-10-03 16:46:26'),
(12, 'Web', 'web', '2026-10-03 16:46:26', '2026-10-03 16:46:26');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;

-- Dữ liệu cho bảng `posts` (15 dòng)
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`id`, `author_id`, `category_id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `status`, `rejection_reason`, `is_featured`, `published_at`, `view_count`, `created_at`, `updated_at`, `deleted_at`, `meta_title`, `meta_description`, `show_thumbnail_in_post`) VALUES
(1, 3, 4, 'Laravel hiện đại cho ứng dụng tin tức', 'laravel-hien-dai-cho-ung-dung-tin-tuc', 'Khám phá cách tổ chức ứng dụng tin tức với Laravel, Eloquent và các quy tắc bảo mật phổ biến.', 'Khám phá cách tổ chức ứng dụng tin tức với Laravel, Eloquent và các quy tắc bảo mật phổ biến. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 1, '2026-10-03 04:13:04', 4, '2026-10-03 16:46:26', '2026-10-04 13:56:41', NULL, 'Laravel hiện đại: nền tảng vững chắc cho ứng dụng tin tức', 'Khám phá cách tổ chức ứng dụng tin tức với Laravel, Eloquent và các quy tắc bảo mật phổ biến.', 1),
(2, 3, 2, 'Mô hình AI nhỏ giúp tối ưu chi phí cho doanh nghiệp', 'mo-hinh-ai-nho-toi-uu-chi-phi', 'Các mô hình gọn nhẹ đang mở ra cách triển khai AI thực tế, tiết kiệm hạ tầng và dễ kiểm soát dữ liệu.', 'Các mô hình gọn nhẹ đang mở ra cách triển khai AI thực tế, tiết kiệm hạ tầng và dễ kiểm soát dữ liệu. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 1, '2026-10-02 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Mô hình AI nhỏ giúp doanh nghiệp tối ưu chi phí', 'Các mô hình gọn nhẹ đang mở ra cách triển khai AI thực tế, tiết kiệm hạ tầng và dễ kiểm soát dữ liệu.', 1),
(3, 3, 3, 'Năm nguyên tắc bảo vệ tài khoản trước tấn công lừa đảo', 'nam-nguyen-tac-bao-ve-tai-khoan', 'Mật khẩu riêng biệt, xác thực đa yếu tố và thói quen kiểm tra liên kết giúp giảm đáng kể rủi ro bị chiếm tài khoản.', 'Mật khẩu riêng biệt, xác thực đa yếu tố và thói quen kiểm tra liên kết giúp giảm đáng kể rủi ro bị chiếm tài khoản. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-10-01 04:13:04', 1, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Năm nguyên tắc bảo vệ tài khoản trước lừa đảo', 'Mật khẩu riêng biệt, xác thực đa yếu tố và thói quen kiểm tra liên kết giúp giảm đáng kể rủi ro bị chiếm tài khoản.', 1),
(4, 3, 6, 'Điện toán đám mây thay đổi cách đội ngũ phát hành phần mềm', 'dien-toan-dam-may-thay-doi-phat-hanh-phan-mem', 'Tự động hóa hạ tầng, quan sát hệ thống và triển khai liên tục giúp đội ngũ phần mềm phát hành ổn định hơn.', 'Tự động hóa hạ tầng, quan sát hệ thống và triển khai liên tục giúp đội ngũ phần mềm phát hành ổn định hơn. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-30 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Điện toán đám mây thay đổi quy trình phát hành phần mềm', 'Tự động hóa hạ tầng, quan sát hệ thống và triển khai liên tục giúp đội ngũ phần mềm phát hành ổn định hơn.', 1),
(5, 3, 7, 'Từ dữ liệu thô đến quyết định kinh doanh có giá trị', 'tu-du-lieu-tho-den-quyet-dinh-kinh-doanh', 'Một quy trình dữ liệu tốt cần bắt đầu từ chất lượng dữ liệu, cách đo lường và câu hỏi kinh doanh rõ ràng.', 'Một quy trình dữ liệu tốt cần bắt đầu từ chất lượng dữ liệu, cách đo lường và câu hỏi kinh doanh rõ ràng. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-29 04:13:04', 2, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Từ dữ liệu thô đến quyết định kinh doanh có giá trị', 'Một quy trình dữ liệu tốt cần bắt đầu từ chất lượng dữ liệu, cách đo lường và câu hỏi kinh doanh rõ ràng.', 1),
(6, 3, 4, 'Thiết kế API dễ bảo trì cho sản phẩm phát triển nhanh', 'thiet-ke-api-de-bao-tri', 'Phiên bản API, validation, phân quyền và tài liệu nhất quán là nền tảng để sản phẩm mở rộng mà không tạo nợ kỹ thuật.', 'Phiên bản API, validation, phân quyền và tài liệu nhất quán là nền tảng để sản phẩm mở rộng mà không tạo nợ kỹ thuật. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-28 04:13:04', 1, '2026-10-03 16:46:27', '2026-10-04 04:50:29', NULL, 'Thiết kế API dễ bảo trì cho sản phẩm phát triển nhanh', 'Phiên bản API, validation, phân quyền và tài liệu nhất quán là nền tảng để sản phẩm mở rộng mà không tạo nợ kỹ thuật.', 1),
(7, 3, 5, 'Những điểm cần kiểm tra khi chọn laptop cho lập trình viên', 'chon-laptop-cho-lap-trinh-vien', 'CPU, bộ nhớ, khả năng nâng cấp và thời lượng pin là các tiêu chí quan trọng hơn thông số quảng cáo đơn lẻ.', 'CPU, bộ nhớ, khả năng nâng cấp và thời lượng pin là các tiêu chí quan trọng hơn thông số quảng cáo đơn lẻ. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-27 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Những điểm cần kiểm tra khi chọn laptop cho lập trình viên', 'CPU, bộ nhớ, khả năng nâng cấp và thời lượng pin là các tiêu chí quan trọng hơn thông số quảng cáo đơn lẻ.', 1),
(8, 3, 3, 'Thiết bị IoT và bài toán bảo mật ngay từ thiết kế', 'thiet-bi-iot-va-bao-mat-ngay-tu-thiet-ke', 'Quản lý danh tính thiết bị, cập nhật firmware và giới hạn quyền truy cập là ba lớp bảo vệ cần có trong hệ thống IoT.', 'Quản lý danh tính thiết bị, cập nhật firmware và giới hạn quyền truy cập là ba lớp bảo vệ cần có trong hệ thống IoT. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 1, '2026-09-26 04:13:04', 1, '2026-10-03 16:46:27', '2026-10-04 09:24:25', NULL, 'Thiết bị IoT cần được bảo mật ngay từ thiết kế', 'Quản lý danh tính thiết bị, cập nhật firmware và giới hạn quyền truy cập là ba lớp bảo vệ cần có trong hệ thống IoT.', 1),
(9, 3, 8, 'Startup công nghệ nên đo lường điều gì trước khi mở rộng', 'startup-cong-nghe-do-luong-truoc-khi-mo-rong', 'Tăng trưởng bền vững cần gắn với tỷ lệ giữ chân, chi phí thu hút khách hàng và giá trị sản phẩm mang lại.', 'Tăng trưởng bền vững cần gắn với tỷ lệ giữ chân, chi phí thu hút khách hàng và giá trị sản phẩm mang lại. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-25 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Startup công nghệ nên đo lường điều gì trước khi mở rộng', 'Tăng trưởng bền vững cần gắn với tỷ lệ giữ chân, chi phí thu hút khách hàng và giá trị sản phẩm mang lại.', 1),
(10, 3, 4, 'Mã nguồn mở giúp đội ngũ nhỏ tăng tốc như thế nào', 'ma-nguon-mo-giup-doi-ngu-nho-tang-toc', 'Sử dụng thư viện mở đúng cách giúp tiết kiệm thời gian, nhưng vẫn cần kiểm tra giấy phép, bảo mật và khả năng bảo trì.', 'Sử dụng thư viện mở đúng cách giúp tiết kiệm thời gian, nhưng vẫn cần kiểm tra giấy phép, bảo mật và khả năng bảo trì. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-24 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Mã nguồn mở giúp đội ngũ nhỏ tăng tốc như thế nào', 'Sử dụng thư viện mở đúng cách giúp tiết kiệm thời gian, nhưng vẫn cần kiểm tra giấy phép, bảo mật và khả năng bảo trì.', 1),
(11, 3, 9, 'Chuyển đổi số bắt đầu từ quy trình chứ không chỉ từ phần mềm', 'chuyen-doi-so-bat-dau-tu-quy-trinh', 'Công nghệ chỉ tạo ra giá trị khi giải quyết đúng điểm nghẽn và được người dùng nội bộ chấp nhận.', 'Công nghệ chỉ tạo ra giá trị khi giải quyết đúng điểm nghẽn và được người dùng nội bộ chấp nhận. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-23 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Chuyển đổi số bắt đầu từ quy trình chứ không chỉ từ phần mềm', 'Công nghệ chỉ tạo ra giá trị khi giải quyết đúng điểm nghẽn và được người dùng nội bộ chấp nhận.', 1),
(12, 3, 2, 'Ứng dụng AI có trách nhiệm trong tòa soạn số', 'ung-dung-ai-co-trach-nhiem-trong-toa-soan', 'AI có thể hỗ trợ phân loại và gợi ý nội dung, nhưng biên tập viên vẫn cần kiểm chứng nguồn và chịu trách nhiệm cuối cùng.', 'AI có thể hỗ trợ phân loại và gợi ý nội dung, nhưng biên tập viên vẫn cần kiểm chứng nguồn và chịu trách nhiệm cuối cùng. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-22 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Ứng dụng AI có trách nhiệm trong tòa soạn số', 'AI có thể hỗ trợ phân loại và gợi ý nội dung, nhưng biên tập viên vẫn cần kiểm chứng nguồn và chịu trách nhiệm cuối cùng.', 1),
(13, 3, 6, 'DevOps và văn hóa cải tiến liên tục trong nhóm kỹ thuật', 'devops-va-van-hoa-cai-tien-lien-tuc', 'DevOps không chỉ là công cụ triển khai mà còn là cách phối hợp để phát hiện lỗi sớm và cải thiện liên tục.', 'DevOps không chỉ là công cụ triển khai mà còn là cách phối hợp để phát hiện lỗi sớm và cải thiện liên tục. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-21 04:13:04', 0, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'DevOps và văn hóa cải tiến liên tục trong nhóm kỹ thuật', 'DevOps không chỉ là công cụ triển khai mà còn là cách phối hợp để phát hiện lỗi sớm và cải thiện liên tục.', 1),
(14, 3, 9, 'Tự động hóa báo cáo giúp doanh nghiệp tiết kiệm thời gian', 'tu-dong-hoa-bao-cao-doanh-nghiep', 'Chuẩn hóa dữ liệu đầu vào và thiết lập chỉ số rõ ràng giúp báo cáo tự động đáng tin cậy hơn.', 'Chuẩn hóa dữ liệu đầu vào và thiết lập chỉ số rõ ràng giúp báo cáo tự động đáng tin cậy hơn. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-20 04:13:04', 1, '2026-10-03 16:46:27', '2026-10-04 04:13:04', NULL, 'Tự động hóa báo cáo giúp doanh nghiệp tiết kiệm thời gian', 'Chuẩn hóa dữ liệu đầu vào và thiết lập chỉ số rõ ràng giúp báo cáo tự động đáng tin cậy hơn.', 1),
(15, 3, 1, 'Xu hướng nghề nghiệp lập trình PHP trong sản phẩm web', 'xu-huong-nghe-nghiep-lap-trinh-php', 'Nền tảng PHP hiện đại yêu cầu lập trình viên hiểu cả kiến trúc, kiểm thử, bảo mật và vận hành sản phẩm.', 'Nền tảng PHP hiện đại yêu cầu lập trình viên hiểu cả kiến trúc, kiểm thử, bảo mật và vận hành sản phẩm. Bài viết cung cấp góc nhìn tổng quan và các gợi ý thực hành phù hợp cho độc giả NewsHub.', NULL, 'published', NULL, 0, '2026-09-19 04:13:04', 1, '2026-10-03 16:46:27', '2026-10-04 13:56:42', NULL, 'Xu hướng nghề nghiệp lập trình PHP trong sản phẩm web', 'Nền tảng PHP hiện đại yêu cầu lập trình viên hiểu cả kiến trúc, kiểm thử, bảo mật và vận hành sản phẩm.', 1);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;

-- Dữ liệu cho bảng `post_tag` (31 dòng)
/*!40000 ALTER TABLE `post_tag` DISABLE KEYS */;
INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES
(1, 1),
(1, 2),
(1, 12),
(2, 3),
(2, 9),
(3, 5),
(4, 6),
(4, 7),
(5, 3),
(5, 9),
(6, 2),
(6, 8),
(6, 12),
(7, 7),
(8, 5),
(8, 11),
(9, 4),
(9, 9),
(10, 2),
(10, 8),
(11, 4),
(11, 6),
(12, 3),
(12, 5),
(13, 6);
INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES
(13, 7),
(14, 3),
(14, 9),
(15, 1),
(15, 2),
(15, 12);
/*!40000 ALTER TABLE `post_tag` ENABLE KEYS */;

-- Dữ liệu cho bảng `comments` (4 dòng)
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `parent_id`, `reply_to_id`, `content`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 2, NULL, NULL, 'a', 'visible', '2026-10-03 16:53:38', '2026-10-03 17:51:56', '2026-10-03 17:51:56'),
(2, 1, 4, NULL, NULL, '1 2 3', 'visible', '2026-10-04 04:43:17', '2026-10-04 04:43:17', NULL),
(3, 1, 4, 2, 2, '2 3 4', 'visible', '2026-10-04 04:43:27', '2026-10-04 04:43:27', NULL),
(4, 1, 4, 2, 3, '1 2 3', 'hidden', '2026-10-04 04:43:38', '2026-10-04 10:15:47', NULL);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;

-- Dữ liệu cho bảng `comment_reports` (1 dòng)
/*!40000 ALTER TABLE `comment_reports` DISABLE KEYS */;
INSERT INTO `comment_reports` (`id`, `comment_id`, `reporter_id`, `reason`, `description`, `status`, `handled_by`, `handled_at`, `created_at`, `updated_at`) VALUES
(1, 4, 2, 'spam', NULL, 'resolved', 2, '2026-10-04 10:15:47', '2026-10-04 04:54:12', '2026-10-04 10:15:47');
/*!40000 ALTER TABLE `comment_reports` ENABLE KEYS */;

-- Dữ liệu cho bảng `favorites` (2 dòng)
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` (`id`, `user_id`, `post_id`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2026-10-04 04:50:36', '2026-10-04 04:50:36'),
(2, 2, 8, '2026-10-04 09:24:31', '2026-10-04 09:24:31');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;

-- Dữ liệu cho bảng `post_views` (11 dòng)
/*!40000 ALTER TABLE `post_views` DISABLE KEYS */;
INSERT INTO `post_views` (`id`, `post_id`, `user_id`, `session_id`, `ip_hash`, `viewed_at`) VALUES
(1, 1, 1, 'ea2b7a6f-c607-4b17-93a9-f93e31e74027', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-03 16:49:51'),
(2, 5, 1, 'ea2b7a6f-c607-4b17-93a9-f93e31e74027', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-03 16:50:42'),
(3, 3, 2, 'c5a3cfa2-5783-4a14-bbdf-78e3efee49f4', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-03 16:51:59'),
(4, 5, 2, 'c5a3cfa2-5783-4a14-bbdf-78e3efee49f4', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-03 16:53:36'),
(5, 14, 2, 'c5a3cfa2-5783-4a14-bbdf-78e3efee49f4', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-03 17:46:57'),
(6, 1, 4, '5020d8fb-9f81-4028-be1a-2e9af801797a', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 04:43:11'),
(7, 6, 4, '5020d8fb-9f81-4028-be1a-2e9af801797a', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 04:50:29'),
(8, 1, 2, 'f80ed2a1-d5af-4f28-8b2a-b6c7e4ebda13', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 04:54:06'),
(9, 8, 2, '931601c1-ed60-4629-b8a3-e553a0f31e06', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 09:24:25'),
(10, 1, 3, '38d6988b-e8d3-42c7-9a4a-ba84ab80981d', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 13:56:41'),
(11, 15, 3, '38d6988b-e8d3-42c7-9a4a-ba84ab80981d', 'e181589457da407db2ea98f49f8b267743cf69f88a34b0d9b095dfe4f6ffee31', '2026-10-04 13:56:42');
/*!40000 ALTER TABLE `post_views` ENABLE KEYS */;

-- Dữ liệu cho bảng `activity_logs` (5 dòng)
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `subject_type`, `subject_id`, `description`, `created_at`) VALUES
(1, 2, 'comment.delete', 'App\\Models\\Comment', 1, 'Admin kiểm duyệt bình luận.', '2026-10-03 17:51:56'),
(2, 2, 'post.featured', 'App\\Models\\Post', 8, 'Ghim tiêu điểm bài viết: Thiết bị IoT và bài toán bảo mật ngay từ thiết kế', '2026-10-04 09:12:39'),
(3, 3, 'post_request.created', 'App\\Models\\PostRequest', 1, 'Tác giả đã gửi yêu cầu correction cho bài viết.', '2026-10-04 10:08:27'),
(4, 2, 'comment-report.hide', 'App\\Models\\Comment', 4, 'Đã xử lý báo cáo bình luận.', '2026-10-04 10:15:47'),
(5, 3, 'post_request.cancelled', 'App\\Models\\PostRequest', 1, 'Tác giả đã hủy yêu cầu correction cho bài viết.', '2026-10-04 13:37:37');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;

-- Dữ liệu cho bảng `author_applications` (1 dòng)
/*!40000 ALTER TABLE `author_applications` DISABLE KEYS */;
INSERT INTO `author_applications` (`id`, `user_id`, `pending_user_id`, `category_id`, `bio`, `sample_title`, `sample_content`, `status`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 4, 4, 3, '1 ead', 'ađâ', 'dâđâ', 'pending', NULL, NULL, NULL, '2026-10-04 04:51:05', '2026-10-04 04:51:05');
/*!40000 ALTER TABLE `author_applications` ENABLE KEYS */;

-- Dữ liệu cho bảng `post_requests` (1 dòng)
/*!40000 ALTER TABLE `post_requests` DISABLE KEYS */;
INSERT INTO `post_requests` (`id`, `post_id`, `pending_post_id`, `author_id`, `type`, `priority`, `reason`, `notes`, `status`, `admin_notes`, `handled_by`, `handled_at`, `created_at`, `updated_at`) VALUES
(1, 15, NULL, 3, 'correction', 'normal', 'đă', 'dâdd', 'cancelled', NULL, NULL, '2026-10-04 13:37:37', '2026-10-04 10:08:27', '2026-10-04 13:37:37');
/*!40000 ALTER TABLE `post_requests` ENABLE KEYS */;

-- ============================================================================
-- 5. PHỤC HỒI CÀI ĐẶT HỆ THỐNG
-- ============================================================================
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
-- ============================================================================
-- Hoàn tất khởi tạo cơ sở dữ liệu NewsHub!
-- ============================================================================