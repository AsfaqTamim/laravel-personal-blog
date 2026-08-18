<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'posts' => Post::count(),
            'published_posts' => Post::where('is_published', true)->count(),
            'categories' => Category::count(),
            'comments' => Comment::count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
            'users' => User::count(),
        ];

        $recentPosts = Post::with('categories')
            ->latest()
            ->limit(5)
            ->get();

        $pendingComments = Comment::with('post')
            ->where('is_approved', false)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'pendingComments'));
    }
}
