@extends('layouts.blog')

@section('title', $post->title)
@section('description', Str::limit($post->excerpt ?? strip_tags($post->body), 160))
@section('og_image', $post->featured_image_url)

@section('structured_data')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BlogPosting",
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@@id": {!! json_ld(route('blog.show', $post)) !!}
        },
        "headline": {!! json_ld($post->title) !!},
        "description": {!! json_ld(Str::limit($post->excerpt ?? strip_tags($post->body), 160)) !!},
        "image": {!! json_ld($post->featured_image_url) !!},
        "author": {
            "@@type": "Person",
            "name": {!! json_ld($post->user?->name ?? 'Clara Dawson') !!},
            "url": {!! json_ld(url('/')) !!}
        },
        "publisher": {
            "@@type": "Person",
            "name": {!! json_ld(blog_setting('site_name', config('app.name', 'Clara Dawson'))) !!},
            "url": {!! json_ld(url('/')) !!}
        },
        "datePublished": {!! json_ld(optional($post->published_at)->toIso8601String()) !!},
        "dateModified": {!! json_ld($post->updated_at->toIso8601String()) !!}
    }
    </script>
@endsection

@section('content')
    <div class="container">
        <header class="article-header">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('blog.index') }}">Home</a>
                @foreach ($post->categories->take(1) as $category)
                    <i class="fa-solid fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
                    <a href="{{ route('blog.category', $category) }}">{{ $category->name }}</a>
                @endforeach
            </nav>

            @if ($post->categories->isNotEmpty())
                <span class="kicker">{{ $post->categories->first()->name }}</span>
            @endif

            <h1 class="article-title">{{ $post->title }}</h1>

            <div class="article-byline">
                <span class="avatar">{{ strtoupper(substr($post->user?->name ?? 'A', 0, 1)) }}</span>
                <span class="byline-box">
                    <span class="byline-name">By {{ $post->user?->name ?? 'Admin' }}</span>
                    <span class="byline-date">{{ $post->published_at?->format('F j, Y') }}
                        · {{ max(1, (int) ceil(str_word_count(strip_tags($post->body)) / 200)) }} min read
                        · <i class="fa-regular fa-eye" aria-hidden="true"></i> {{ number_format($post->views_count) }} views</span>
                </span>
            </div>

            <div class="share-row">
                <span>Share:</span>
                <a class="share-btn" href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post)) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i> Post
                </a>
                <a class="share-btn" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i> Share
                </a>
                <a class="share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i> Share
                </a>
                <button type="button" class="share-btn"
                    onclick="if (navigator.clipboard) { navigator.clipboard.writeText(location.href); var b = this; b.innerHTML = '<i class=&quot;fa-solid fa-check&quot; aria-hidden=&quot;true&quot;></i> Copied'; setTimeout(function () { b.innerHTML = '<i class=&quot;fa-solid fa-link&quot; aria-hidden=&quot;true&quot;></i> Copy Link'; }, 2000); }">
                    <i class="fa-solid fa-link" aria-hidden="true"></i> Copy Link
                </button>
            </div>

            <div class="article-rule"></div>
        </header>

        <div class="article-wrap">
            @if ($post->featured_image_url)
                <figure class="featured">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}">
                </figure>
            @endif

            <div class="prose">
                @if ($post->body !== strip_tags($post->body))
                    {{-- Rich HTML content from the Summernote editor --}}
                    {!! $post->body !!}
                @else
                    {{-- Plain text from older posts --}}
                    @foreach (preg_split('/\R\s*\R/', trim($post->body), -1, PREG_SPLIT_NO_EMPTY) as $paragraph)
                        <p>{!! nl2br(e(trim($paragraph))) !!}</p>
                    @endforeach
                @endif
            </div>

            <div class="article-foot">
                <a href="{{ route('blog.index') }}" class="back-link"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to Blog</a>
                @if ($post->categories->isNotEmpty())
                    <div class="chips">
                        @foreach ($post->categories as $category)
                            <a href="{{ route('blog.category', $category) }}" class="chip">{{ $category->name }}</a>
                        @endforeach
                    </div>
                @endif
                @if ($post->tags->isNotEmpty())
                    <div class="chips">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag) }}" class="chip"><i class="fa-solid fa-tag" aria-hidden="true"></i> {{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <section class="comments" id="comments">
            <div class="comments-head">
                <h2>Comments</h2>
                <span class="count-pill">{{ $post->comments->count() }}</span>
            </div>

            @if (session('comment_status'))
                <div class="alert alert-success">{{ session('comment_status') }}</div>
            @endif

            @if ($post->comments->isEmpty())
                <p class="empty-hint">No comments yet — be the first to share your thoughts!</p>
            @else
                <ul class="comment-list">
                    @foreach ($post->comments as $comment)
                        <li class="comment-item">
                            <span class="comment-avatar">{{ strtoupper(substr($comment->name, 0, 1)) }}</span>
                            <div class="comment-main">
                                <div class="comment-head">
                                    <span class="comment-name">{{ $comment->name }}</span>
                                    <span class="comment-date">{{ $comment->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="comment-body">{{ $comment->body }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="comment-form">
                <h3>Leave a comment</h3>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('blog.comments.store', $post) }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-input"
                                value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required maxlength="255">
                        </div>
                        <div class="form-field">
                            <label for="email">Email <small>(not published)</small></label>
                            <input type="email" id="email" name="email" class="form-input"
                                value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required maxlength="255">
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="body">Comment</label>
                        <textarea id="body" name="body" class="form-input" rows="5" required maxlength="2000">{{ old('body') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Post Comment</button>
                </form>
            </div>
        </section>
    </div>
@endsection
