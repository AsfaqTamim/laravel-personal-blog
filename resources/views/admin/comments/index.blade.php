@extends('admin.layouts.app')

@section('title', 'Comments')
@section('topbar-title', 'Comments')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Comments</h1>
            <p class="page-sub">Moderate reader comments on your posts.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <div class="panel-body">
            <div class="filter-tabs">
                <a href="{{ route('admin.comments.index') }}"
                    class="filter-tab {{ request('status') === null ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
                    class="filter-tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
                    class="filter-tab {{ request('status') === 'approved' ? 'active' : '' }}">Approved</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Commenter</th>
                        <th>On Post</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $comment->name }}</div>
                                <div class="cell-sub">{{ $comment->email }}</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.posts.edit', $comment->post) }}">
                                    {{ Str::limit($comment->post?->title ?? '—', 40) }}
                                </a>
                            </td>
                            <td>
                                <div class="comment-body">{{ Str::limit($comment->body, 120) }}</div>
                            </td>
                            <td>
                                @if ($comment->is_approved)
                                    <span class="badge badge-published">Approved</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            </td>
                            <td>{{ $comment->created_at->format('M d, Y') }}</td>
                            <td class="text-right">
                                @if ($comment->is_approved)
                                    <form method="POST" action="{{ route('admin.comments.unapprove', $comment) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">Unapprove</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.comments.approve', $comment) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                    </form>
                                @endif
                                <form
                                    method="POST"
                                    action="{{ route('admin.comments.destroy', $comment) }}"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete this comment permanently?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">No comments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($comments->hasPages())
            <div class="panel-foot">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
@endsection
