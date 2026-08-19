@extends('admin.layouts.app')

@section('title', 'Edit Tag')
@section('topbar-title', 'Tags')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Tag</h1>
            <p class="page-sub">{{ $tag->posts()->count() }} post(s) with this tag.</p>
        </div>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline">← Back to Tags</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
                @csrf
                @method('PUT')
                @include('admin.tags._form')
                <button type="submit" class="btn btn-primary">Update Tag</button>
            </form>
        </div>
    </div>
@endsection
