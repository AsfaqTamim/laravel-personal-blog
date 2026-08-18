<article class="news-card reveal">
    <a href="{{ route('blog.show', $post) }}" class="news-media" aria-label="{{ $post->title }}">
        @if ($post->featured_image_url)
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="news-media-letter">{{ strtoupper(substr($post->title, 0, 1)) }}</span>
        @endif
    </a>

    <div class="news-body">
        @if ($post->categories->isNotEmpty())
            <span class="kicker">{{ $post->categories->first()->name }}</span>
        @endif

        <h2 class="news-title">
            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
        </h2>

        @if ($post->excerpt)
            <p class="news-excerpt">{{ Str::limit($post->excerpt, 130) }}</p>
        @endif

        <div class="news-meta">
            <span class="byline">By {{ $post->user?->name ?? 'Admin' }}</span>
            <span class="meta-sep">|</span>
            <span>{{ $post->published_at?->format('M j, Y') }}</span>
            <span class="news-read">{{ max(1, (int) ceil(str_word_count(strip_tags($post->body)) / 200)) }} min read</span>
        </div>
    </div>
</article>
