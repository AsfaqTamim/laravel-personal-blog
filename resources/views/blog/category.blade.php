@extends('layouts.blog')

@section('title', 'Category: '.$category->name)
@section('description', 'All articles filed under '.$category->name.' ('.$posts->total().' post'.($posts->total() === 1 ? '' : 's').').')

@section('content')
    <section class="page-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                <span>Category</span>
            </nav>
            <h1>{{ $category->name }}</h1>
            <p>{{ $posts->total() }} article{{ $posts->total() === 1 ? '' : 's' }} in this category.</p>
        </div>
    </section>

    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Latest in <span class="accent-dot">{{ $category->name }}</span></h2>
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
                <div class="empty-icon"><i class="fa-solid fa-folder-open" aria-hidden="true"></i></div>
                <h2>Nothing here yet</h2>
                <p>No posts in this category. <a href="{{ route('blog.index') }}">Browse all articles →</a></p>
            </div>
        @endif
    </div>
@endsection
