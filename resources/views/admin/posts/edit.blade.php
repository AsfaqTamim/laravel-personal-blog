@extends('admin.layouts.app')

@section('title', 'Edit Post')
@section('topbar-title', 'Posts')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Post</h1>
            <p class="page-sub">
                @if ($post->is_published)
                    Published on {{ $post->published_at?->format('M d, Y') }}.
                @else
                    Currently a draft.
                @endif
            </p>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline">← Back to Posts</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.posts._form')
                <button type="submit" class="btn btn-primary">Update Post</button>
            </form>
        </div>
    </div>
@endsection
