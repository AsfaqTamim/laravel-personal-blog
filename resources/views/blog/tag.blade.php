@extends('layouts.blog')

@section('title', 'Tag: '.$tag->name)
@section('description', 'All articles tagged '.$tag->name.' ('.$posts->total().' post'.($posts->total() === 1 ? '' : 's').').')

@section('content')
    <section class="page-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                <span>Tags</span>
            </nav>
            <h1><i class="fa-solid fa-tag" aria-hidden="true"></i> {{ $tag->name }}</h1>
            <p>{{ $posts->total() }} article{{ $posts->total() === 1 ? '' : 's' }} with this tag.</p>
        </div>
    </section>

    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Latest in <span class="accent-dot">{{ $tag->name }}</span></h2>
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
                <div class="empty-icon"><i class="fa-solid fa-tag" aria-hidden="true"></i></div>
                <h2>Nothing here yet</h2>
                <p>No posts with this tag. <a href="{{ route('blog.index') }}">Browse all articles →</a></p>
            </div>
        @endif
    </div>
@endsection
