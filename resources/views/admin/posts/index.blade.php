@extends('admin.layouts.app')

@section('title', 'Posts')
@section('topbar-title', 'Posts')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Posts</h1>
            <p class="page-sub">{{ $posts->total() }} post(s) total.</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">＋ New Post</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.posts.index') }}" class="table-toolbar">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search posts by title or excerpt…"
                    class="form-input"
                    style="max-width: 320px;"
                >
                <button type="submit" class="btn btn-outline">Search</button>
                @if (request('q'))
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline">Clear</a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Categories</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Published</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $post->title }}</div>
                                <div class="cell-sub">/{{ $post->slug }}</div>
                            </td>
                            <td>
                                @forelse ($post->categories as $category)
                                    <span class="chip">{{ $category->name }}</span>
                                @empty
                                    <span class="cell-sub">—</span>
                                @endforelse
                            </td>
                            <td>{{ $post->user?->name ?? '—' }}</td>
                            <td>
                                @if ($post->is_published)
                                    <span class="badge badge-published">Published</span>
                                @else
                                    <span class="badge badge-draft">Draft</span>
                                @endif
                            </td>
                            <td>{{ number_format($post->views_count) }}</td>
                            <td>{{ $post->published_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.posts.destroy', $post) }}"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete this post permanently?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">
                                No posts found.
                                <a href="{{ route('admin.posts.create') }}">Create your first post →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="panel-foot">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection
