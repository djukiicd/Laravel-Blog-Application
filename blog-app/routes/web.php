<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UpvoteController;
use Illuminate\Support\Facades\Route;

// Redirect root to posts index
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Public routes for posts (viewable by everyone)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/results', [SearchController::class, 'search'])->name('search.results');

// Authenticated routes for posts
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Comment routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Upvote routes
    Route::post('/posts/{post}/upvote', [UpvoteController::class, 'toggle'])->name('posts.upvote');
});

// Public route for viewing individual posts (must come after authenticated routes)
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Dashboard route
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Get user's posts with counts
    $userPosts = $user->posts()
        ->withCount(['upvotes', 'comments'])
        ->latest()
        ->paginate(5);
    
    // Get user's comments with post info
    $userComments = $user->comments()
        ->with('post')
        ->latest()
        ->paginate(5);
    
    // Calculate total upvotes received by user's posts
    $totalUpvotes = $user->posts()
        ->withCount('upvotes')
        ->get()
        ->sum('upvotes_count');
    
    // Calculate recent activity (posts + comments in last 7 days)
    $recentActivity = $user->posts()
        ->where('created_at', '>=', now()->subDays(7))
        ->count() + 
        $user->comments()
        ->where('created_at', '>=', now()->subDays(7))
        ->count();
    
    return view('dashboard', compact('userPosts', 'userComments', 'totalUpvotes', 'recentActivity'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::delete('/posts/{post}', [AdminController::class, 'deletePost'])->name('posts.delete');
    Route::delete('/comments/{comment}', [AdminController::class, 'deleteComment'])->name('comments.delete');
    Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
});

require __DIR__.'/auth.php';
