<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VisitController;
use App\Http\Middleware\TrackVisit;
use Illuminate\Support\Facades\Route;

// Visitor telemetry (reports Wi-Fi vs cellular after page load)
Route::get('/visit-network', [VisitController::class, 'network'])->name('visit.network');

// Public blog — every page view is tracked for visitor analytics
Route::middleware(TrackVisit::class)->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog', [BlogController::class, 'all'])->name('blog.all');
    Route::get('/categories', [BlogController::class, 'categories'])->name('blog.categories');
    Route::get('/search', [BlogController::class, 'search'])->name('blog.search');
    Route::get('/feed', [BlogController::class, 'feed'])->name('blog.feed');
    Route::get('/sitemap.xml', [BlogController::class, 'sitemap'])->name('blog.sitemap');
    Route::get('/robots.txt', [BlogController::class, 'robots'])->name('blog.robots');
    Route::get('/posts/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/categories/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/tags/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');
    Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('blog.comments.store');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

// Authenticated routes (admin panel)
Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Posts management
    Route::prefix('admin/posts')->name('admin.posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
    });

    // Categories management
    Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Tags management
    Route::prefix('admin/tags')->name('admin.tags.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::get('/create', [TagController::class, 'create'])->name('create');
        Route::post('/', [TagController::class, 'store'])->name('store');
        Route::get('/{tag}/edit', [TagController::class, 'edit'])->name('edit');
        Route::put('/{tag}', [TagController::class, 'update'])->name('update');
        Route::delete('/{tag}', [TagController::class, 'destroy'])->name('destroy');
    });

    // Comments moderation
    Route::prefix('admin/comments')->name('admin.comments.')->group(function () {
        Route::get('/', [AdminCommentController::class, 'index'])->name('index');
        Route::post('/{comment}/approve', [AdminCommentController::class, 'approve'])->name('approve');
        Route::post('/{comment}/unapprove', [AdminCommentController::class, 'unapprove'])->name('unapprove');
        Route::delete('/{comment}', [AdminCommentController::class, 'destroy'])->name('destroy');
    });

    // Visitor analytics
    Route::prefix('admin/visitors')->name('admin.visitors.')->group(function () {
        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::delete('/', [VisitorController::class, 'clear'])->name('clear');
    });

    // Users management
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Blog settings
    Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::get('/seo', [SettingsController::class, 'seo'])->name('seo');
        Route::put('/seo', [SettingsController::class, 'updateSeo'])->name('seo.update');
    });
});
