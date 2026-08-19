@extends('admin.layouts.app')

@section('title', 'New Tag')
@section('topbar-title', 'Tags')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create Tag</h1>
            <p class="page-sub">Group related posts with a tag.</p>
        </div>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline">← Back to Tags</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.tags.store') }}">
                @csrf
                @include('admin.tags._form')
                <button type="submit" class="btn btn-primary">Save Tag</button>
            </form>
        </div>
    </div>
@endsection
