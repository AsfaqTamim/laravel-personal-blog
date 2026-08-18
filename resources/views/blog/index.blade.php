@extends('layouts.blog')

@section('title', 'Home')

@section('content')
    <div class="container">
        @if ($posts->count())
            @php($lead = $posts->first())
            @php($heroPosts = $posts->slice(1, 2))

            <div class="hero-grid">
                {{-- Left: big feature --}}
                <article class="lead-story reveal">
                    <a href="{{ route('blog.show', $lead) }}" class="lead-media" aria-label="{{ $lead->title }}">
                        @if ($lead->featured_image_url)
                            <img src="{{ $lead->featured_image_url }}" alt="{{ $lead->title }}" loading="lazy">
                        @else
                            <span class="lead-letter">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                        @endif
                    </a>

                    <div class="lead-body">
                        @if ($lead->categories->isNotEmpty())
                            <span class="kicker">{{ $lead->categories->first()->name }}</span>
                        @endif
                        <h1 class="lead-title">
                            <a href="{{ route('blog.show', $lead) }}">{{ $lead->title }}</a>
                        </h1>
                        <p class="lead-excerpt">{{ Str::limit($lead->excerpt ?: strip_tags($lead->body), 220) }}</p>
                        <div class="news-meta">
                            <span class="byline">By {{ $lead->user?->name ?? 'Admin' }}</span>
                            <span class="meta-sep">|</span>
                            <span>{{ $lead->published_at?->format('F j, Y') }}</span>
                            <span class="news-read"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ max(1, (int) ceil(str_word_count(strip_tags($lead->body)) / 200)) }} min read</span>
                        </div>
                    </div>
                </article>

                {{-- Right: two stacked cards --}}
                <div class="hero-side">
                    @foreach ($heroPosts as $heroPost)
                        <article class="hero-side-card reveal">
                            <a href="{{ route('blog.show', $heroPost) }}" class="hero-side-media" aria-label="{{ $heroPost->title }}">
                                @if ($heroPost->featured_image_url)
                                    <img src="{{ $heroPost->featured_image_url }}" alt="{{ $heroPost->title }}" loading="lazy">
                                @else
                                    <span class="hero-side-letter">{{ strtoupper(substr($heroPost->title, 0, 1)) }}</span>
                                @endif
                            </a>
                            <div class="hero-side-body">
                                @if ($heroPost->categories->isNotEmpty())
                                    <span class="kicker">{{ $heroPost->categories->first()->name }}</span>
                                @endif
                                <h2 class="hero-side-title">
                                    <a href="{{ route('blog.show', $heroPost) }}">{{ $heroPost->title }}</a>
                                </h2>
                                <span class="hero-side-meta">
                                    By {{ $heroPost->user?->name ?? 'Admin' }} · {{ $heroPost->published_at?->format('M j, Y') }}
                                    · {{ max(1, (int) ceil(str_word_count(strip_tags($heroPost->body)) / 200)) }} min read
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- All categories strip (hidden on mobile) --}}
        @if ($allCategories->isNotEmpty())
            <section class="category-strip">
                <div class="rule-head">
                    <h2 class="rule-title"><span class="accent-dot">■</span> Browse by Category</h2>
                    <a href="{{ route('blog.categories') }}" class="rule-link">All categories <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>

                <div class="category-strip-grid">
                    @foreach ($allCategories as $category)
                        <a href="{{ route('blog.category', $category) }}" class="category-card reveal">
                            <span class="category-icon"><i class="fa-solid fa-folder" aria-hidden="true"></i></span>
                            <div class="category-info">
                                <span class="category-name">{{ $category->name }}</span>
                                <span class="category-count">{{ $category->published_count }} article{{ $category->published_count === 1 ? '' : 's' }}</span>
                            </div>
                            <i class="fa-solid fa-arrow-right category-arrow" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Most Read: top articles by view count --}}
        @if ($mostRead->isNotEmpty())
            <section class="section-block reveal">
                <div class="rule-head">
                    <h2 class="rule-title"><span class="accent-dot">■</span> Most Read</h2>
                    <a href="{{ route('blog.all') }}" class="rule-link">View all <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>

                <div class="most-read-grid">
                    @foreach ($mostRead as $index => $mostReadPost)
                        <a href="{{ route('blog.show', $mostReadPost) }}" class="most-read-item">
                            <span class="most-read-rank">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="most-read-info">
                                <span class="most-read-title">{{ $mostReadPost->title }}</span>
                                <span class="most-read-meta">
                                    {{ $mostReadPost->categories->first()?->name ?? 'Article' }}
                                    · <i class="fa-regular fa-eye" aria-hidden="true"></i> {{ number_format($mostReadPost->views_count) }} views
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Category sections: one full-width section per category --}}
        @if ($sections->isNotEmpty())
            @foreach ($sections as $section)
                <section class="section-block reveal">
                    <div class="rule-head">
                        <h2 class="rule-title"><span class="accent-dot">■</span> {{ $section->name }}</h2>
                        <a href="{{ route('blog.category', $section) }}" class="rule-link">View all <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>

                    <div class="sec-cards">
                        @foreach ($section->posts->take(3) as $post)
                            @include('blog._section_card', ['post' => $post, 'wide' => false])
                        @endforeach
                    </div>
                </section>
            @endforeach
        @else
            @if (! $posts->count())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-feather-pointed" aria-hidden="true"></i></div>
                    <h2>No posts yet</h2>
                    <p>Check back soon — new articles are on the way.</p>
                </div>
            @endif
        @endif
    </div>
@endsection
