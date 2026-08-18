@extends('layouts.blog')

@section('title', 'Search')
@section('description', 'Search results across all articles.')
@section('noindex', '1')

@section('content')
    <section class="page-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                <span>Search</span>
            </nav>
            <h1>Search Results</h1>
            <p>{{ $posts->total() }} result{{ $posts->total() === 1 ? '' : 's' }} for “{{ $q }}”.</p>
        </div>
    </section>

    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Matching <span class="accent-dot">Articles</span></h2>
        </div>

        @if ($posts->count())
            <div data-infinite-scroll>
                <div class="posts-grid">
                    @foreach ($posts as $post)
                        @include('blog._card', ['post' => $post])
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="js-pagination">
                        {{ $posts->links() }}
                    </div>
                    <div class="scroll-sentinel" data-next-page="{{ $posts->nextPageUrl() }}"></div>
                @endif
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></div>
                <h2>No posts found</h2>
                <p>Try different keywords, or <a href="{{ route('blog.index') }}">browse all articles</a>.</p>
            </div>
        @endif
    </div>
@endsection
