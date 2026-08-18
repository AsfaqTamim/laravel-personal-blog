@extends('layouts.blog')

@section('title', 'Categories')
@section('description', 'Browse articles by topic.')

@section('content')
    <section class="page-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                <span>Categories</span>
            </nav>
            <h1>Categories</h1>
            <p>Browse articles by topic.</p>
        </div>
    </section>

    <div class="container">
        @if ($categories->count())
            <div class="category-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('blog.category', $category) }}" class="category-card">
                        <span class="category-icon"><i class="fa-solid fa-folder" aria-hidden="true"></i></span>
                        <div class="category-info">
                            <span class="category-name">{{ $category->name }}</span>
                            <span class="category-count">
                                {{ $category->posts_count }} article{{ $category->posts_count === 1 ? '' : 's' }}
                            </span>
                        </div>
                        <i class="fa-solid fa-arrow-right category-arrow" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-folder-open" aria-hidden="true"></i></div>
                <h2>No categories yet</h2>
                <p><a href="{{ route('blog.index') }}">Browse all articles</a> in the meantime.</p>
            </div>
        @endif
    </div>
@endsection
