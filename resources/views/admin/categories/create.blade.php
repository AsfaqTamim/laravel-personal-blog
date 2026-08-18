@extends('admin.layouts.app')

@section('title', 'New Category')
@section('topbar-title', 'Categories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create Category</h1>
            <p class="page-sub">Organize your posts into categories.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">← Back to Categories</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                @include('admin.categories._form')
                <button type="submit" class="btn btn-primary">Save Category</button>
            </form>
        </div>
    </div>
@endsection
