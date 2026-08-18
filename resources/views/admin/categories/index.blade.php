@extends('admin.layouts.app')

@section('title', 'Categories')
@section('topbar-title', 'Categories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Categories</h1>
            <p class="page-sub">{{ $categories->total() }} categor{{ $categories->total() === 1 ? 'y' : 'ies' }} total.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">＋ New Category</a>
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
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $category->name }}</div>
                            </td>
                            <td>
                                <div class="cell-sub">/{{ $category->slug }}</div>
                            </td>
                            <td>{{ $category->posts_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Edit</a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    class="inline-form"
                                    onsubmit="return confirm('Delete this category? Posts are not deleted.');"
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
                                No categories yet.
                                <a href="{{ route('admin.categories.create') }}">Create the first one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="panel-foot">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
