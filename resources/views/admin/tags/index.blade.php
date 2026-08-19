@extends('admin.layouts.app')

@section('title', 'Tags')
@section('topbar-title', 'Tags')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Tags</h1>
            <p class="page-sub">{{ $tags->total() }} tag{{ $tags->total() === 1 ? '' : 's' }} total.</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">＋ New Tag</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $tag->name }}</div>
                            </td>
                            <td>
                                <div class="cell-sub">/{{ $tag->slug }}</div>
                            </td>
                            <td>{{ $tag->posts_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.tags.destroy', $tag) }}"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete this tag? Posts are not deleted.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-cell">
                                No tags yet.
                                <a href="{{ route('admin.tags.create') }}">Create the first one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tags->hasPages())
            <div class="panel-foot">
                {{ $tags->links() }}
            </div>
        @endif
    </div>
@endsection
