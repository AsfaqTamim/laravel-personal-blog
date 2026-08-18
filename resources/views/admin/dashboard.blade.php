@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard')

@section('content')
    @php($hour = (int) now()->format('G'))
    @php($greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening'))
    @php($drafts = $stats['posts'] - $stats['published_posts'])

    <div class="page-head">
        <div>
            <h1 class="page-title">Welcome back, {{ Auth::user()->name }} 👋</h1>
            <p class="page-sub">{{ $greeting }} — {{ now()->format('l, F j, Y') }}. Here's what's happening on your blog.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">＋ New Post</a>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline">View Site</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <a href="{{ route('admin.posts.index') }}" class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Total Posts</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['posts'] }}</div>
            <div class="stat-hint">{{ $stats['published_posts'] }} published · {{ $drafts }} drafts</div>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Categories</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['categories'] }}</div>
            <div class="stat-hint">Organize your content</div>
        </a>

        <a href="{{ route('admin.comments.index') }}" class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Comments</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['comments'] }}</div>
            <div class="stat-hint">{{ $stats['pending_comments'] }} awaiting approval</div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Registered Users</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['users'] }}</div>
            <div class="stat-hint">Team &amp; contributors</div>
        </a>
    </div>

    <div class="dash-grid">
        {{-- Recent posts --}}
        <div class="panel">
            <div class="panel-head">
                <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Recent Posts
                <a href="{{ route('admin.posts.index') }}" class="panel-head-link">View all →</a>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPosts as $post)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ Str::limit($post->title, 46) }}</div>
                                    <div class="cell-sub">
                                        @if ($post->categories->isNotEmpty())
                                            {{ $post->categories->pluck('name')->join(', ') }}
                                        @else
                                            No category
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($post->is_published)
                                        <span class="badge badge-published">Published</span>
                                    @else
                                        <span class="badge badge-draft">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline btn-sm">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-cell">
                                    No posts yet. <a href="{{ route('admin.posts.create') }}">Write your first post →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pending comments --}}
        <div class="panel">
            <div class="panel-head">
                <i class="fa-solid fa-comment-dots" aria-hidden="true"></i> Pending Comments
                @if ($stats['pending_comments'] > 0)
                    <span class="badge badge-pending">{{ $stats['pending_comments'] }}</span>
                @endif
                <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="panel-head-link">Review all →</a>
            </div>
            <div class="panel-body">
                @forelse ($pendingComments as $comment)
                    <div class="comment-mini">
                        <div class="mini-avatar">{{ strtoupper(substr($comment->name, 0, 1)) }}</div>
                        <div class="mini-main">
                            <div class="mini-title">{{ $comment->name }}</div>
                            <div class="mini-sub">
                                on <a href="{{ route('admin.posts.edit', $comment->post) }}">{{ Str::limit($comment->post?->title ?? '—', 36) }}</a>
                                · {{ $comment->created_at->diffForHumans() }}
                            </div>
                            <div class="mini-body">{{ Str::limit($comment->body, 90) }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-cell">🎉 All caught up — no pending comments.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="panel">
        <div class="panel-head">
            <i class="fa-solid fa-bolt" aria-hidden="true"></i> Quick Actions
        </div>
        <div class="panel-body">
            <div class="quick-links">
                <a href="{{ route('admin.posts.create') }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-pen" aria-hidden="true"></i></span> Write a Post
                </a>
                <a href="{{ route('admin.categories.create') }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-tag" aria-hidden="true"></i></span> New Category
                </a>
                <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-comment-dots" aria-hidden="true"></i></span> Pending Comments
                    <span class="qc">{{ $stats['pending_comments'] }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-users" aria-hidden="true"></i></span> Manage Users
                </a>
                <a href="{{ route('admin.settings.index') }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-sliders" aria-hidden="true"></i></span> General Settings
                </a>
                <a href="{{ route('admin.settings.seo') }}" class="quick-link">
                    <span class="qi"><i class="fa-solid fa-magnifying-glass-chart" aria-hidden="true"></i></span> SEO Settings
                </a>
            </div>
        </div>
    </div>
@endsection
