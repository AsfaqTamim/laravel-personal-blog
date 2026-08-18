@extends('admin.layouts.app')

@section('title', 'Edit Category')
@section('topbar-title', 'Categories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Category</h1>
            <p class="page-sub">{{ $category->posts()->count() }} post(s) in this category.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">← Back to Categories</a>
    </div>

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')
                @include('admin.categories._form')
                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>
@endsection
