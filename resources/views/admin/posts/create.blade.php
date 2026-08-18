@extends('admin.layouts.app')

@section('title', 'New Post')
@section('topbar-title', 'Posts')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create Post</h1>
            <p class="page-sub">Write a new blog post.</p>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline">← Back to Posts</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.posts._form')
                <button type="submit" class="btn btn-primary">Save Post</button>
            </form>
        </div>
    </div>
@endsection
