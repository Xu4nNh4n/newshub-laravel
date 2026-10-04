<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthorApplicationController as AdminAuthorApplicationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\CommentReportController as AdminCommentReportController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PostFeaturedController;
use App\Http\Controllers\Admin\PostRequestController as AdminPostRequestController;
use App\Http\Controllers\Admin\PostReviewController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Author\MediaController;
use App\Http\Controllers\Author\PostController as AuthorPostController;
use App\Http\Controllers\Author\PostPublicationController;
use App\Http\Controllers\Author\PostRequestController as AuthorPostRequestController;
use App\Http\Controllers\Author\PostSubmissionController;
use App\Http\Controllers\AuthorApplicationController;
use App\Http\Controllers\AuthorProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyCommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePasswordController;
use App\Http\Controllers\ReadingHistoryController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserActivityHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/feed', [RssFeedController::class, 'index'])->name('feed.index');
Route::get('/feed/{category:slug}', [RssFeedController::class, 'category'])->name('feed.category');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/authors/{user}', AuthorProfileController::class)->name('authors.show');

Route::get('/chinh-sach-bao-mat', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/dieu-khoan-dich-vu', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/quy-che-kiem-duyet', [PageController::class, 'moderationPolicy'])->name('pages.moderation-policy');
Route::get('/lien-he', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/lien-he', [PageController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('pages.contact.submit');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:verification'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware('throttle:verification')
        ->name('verification.send');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', ProfilePasswordController::class)->name('profile.password.update');
    Route::get('/my-comments', MyCommentController::class)->name('profile.comments');
    Route::get('/activity-history', UserActivityHistoryController::class)->name('profile.activity');

    Route::get('/reading-history', [ReadingHistoryController::class, 'index'])->name('reading-history.index');
    Route::delete('/reading-history/{post}', [ReadingHistoryController::class, 'destroy'])->name('reading-history.destroy');
    Route::delete('/reading-history', [ReadingHistoryController::class, 'clear'])->name('reading-history.clear');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'patch'], '/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    Route::middleware('verified')->group(function (): void {
        Route::post('/author-applications', [AuthorApplicationController::class, 'store'])
            ->name('author-applications.store');
        Route::get('/preview/posts/{post}', PostPreviewController::class)->name('posts.preview');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/news/{post}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/news/{post}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
        Route::post('/news/{post}/comments', [CommentController::class, 'store'])
            ->middleware('throttle:comments')
            ->name('comments.store');
        Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{comment}/reports', CommentReportController::class)
            ->middleware('throttle:comment-reports')
            ->name('comment-reports.store');
    });

    Route::get('/author', DashboardController::class)
        ->middleware('verified')
        ->name('author.dashboard');

    Route::prefix('author')->name('author.')->middleware(['verified', 'role:author,admin'])->group(function (): void {
        Route::post('media/upload', [MediaController::class, 'upload'])
            ->middleware('throttle:30,1')
            ->name('media.upload');
        Route::resource('posts', AuthorPostController::class)->except('show');
        Route::post('posts/{post}/publish', PostPublicationController::class)->name('posts.publish');
        Route::post('posts/{post}/submission', PostSubmissionController::class)->name('posts.submit');
        Route::post('posts/{post}/withdraw', [PostSubmissionController::class, 'withdraw'])->name('posts.withdraw');
        Route::post('posts/{post}/requests', [AuthorPostRequestController::class, 'store'])
            ->name('posts.requests.store');
        Route::delete('posts/{post}/requests/{postRequest}', [AuthorPostRequestController::class, 'destroy'])
            ->name('posts.requests.destroy');
    });
    Route::get('/admin', DashboardController::class)
        ->middleware(['verified', 'role:admin'])
        ->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->middleware(['verified', 'role:admin'])->group(function (): void {
        Route::get('author-applications', [AdminAuthorApplicationController::class, 'index'])
            ->name('author-applications.index');
        Route::patch('author-applications/{application}/approve', [AdminAuthorApplicationController::class, 'approve'])
            ->name('author-applications.approve');
        Route::patch('author-applications/{application}/reject', [AdminAuthorApplicationController::class, 'reject'])
            ->name('author-applications.reject');
        Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('activity-logs', ActivityLogController::class)->name('activity-logs.index');
        Route::get('posts/export', [AdminPostController::class, 'export'])->name('posts.export');
        Route::get('posts', [AdminPostController::class, 'index'])->name('posts.index');
        Route::patch('posts/{post}/featured', PostFeaturedController::class)->name('posts.toggle-featured');
        Route::patch('posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('tags', TagController::class)->except('show');
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/send-reset-link', [AdminUserController::class, 'sendResetLink'])->name('users.send-reset-link');
        Route::get('post-reviews', [PostReviewController::class, 'index'])->name('post-reviews.index');
        Route::post('post-reviews/{post}/approve', [PostReviewController::class, 'approve'])->name('post-reviews.approve');
        Route::post('post-reviews/{post}/reject', [PostReviewController::class, 'reject'])->name('post-reviews.reject');
        Route::get('post-requests', [AdminPostRequestController::class, 'index'])->name('post-requests.index');
        Route::patch('post-requests/{postRequest}', [AdminPostRequestController::class, 'update'])
            ->name('post-requests.update');
        Route::get('comment-reports', [AdminCommentReportController::class, 'index'])->name('comment-reports.index');
        Route::put('comment-reports/{commentReport}', [AdminCommentReportController::class, 'update'])->name('comment-reports.update');
        Route::get('comments/export', [AdminCommentController::class, 'export'])->name('comments.export');
        Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::patch('comments/{comment}', [AdminCommentController::class, 'update'])->name('comments.update');
    });
});
