@extends('layouts.blog')

@section('title', 'Blog')
@section('description', 'Browse every article published on the blog.')

@section('content')
    <section class="page-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                <span>Blog</span>
            </nav>
            <h1>All Articles</h1>
            <p>{{ $posts->total() }} article{{ $posts->total() === 1 ? '' : 's' }} published.</p>
        </div>
    </section>

    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Browse <span class="accent-dot">Everything</span></h2>
            <a href="{{ route('blog.feed') }}" class="section-link"><i class="fa-solid fa-rss" aria-hidden="true"></i> RSS Feed</a>
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
                <div class="empty-icon"><i class="fa-solid fa-newspaper" aria-hidden="true"></i></div>
                <h2>No articles yet</h2>
                <p>Check back soon — new articles are on the way.</p>
            </div>
        @endif
    </div>
@endsection
