<article class="sec-card reveal {{ $wide ? 'sec-card--wide' : '' }}">
    <a href="{{ route('blog.show', $post) }}" class="sec-media" aria-label="{{ $post->title }}">
        @if ($post->featured_image_url)
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="sec-letter">{{ strtoupper(substr($post->title, 0, 1)) }}</span>
        @endif
    </a>

    <div class="sec-body">
        @if ($post->categories->isNotEmpty())
            <span class="kicker">{{ $post->categories->first()->name }}</span>
        @endif

        <h3 class="sec-title">
            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
        </h3>

        @if ($wide && $post->excerpt)
            <p class="sec-excerpt">{{ Str::limit($post->excerpt, 130) }}</p>
        @endif

        <span class="sec-meta">
            By {{ $post->user?->name ?? 'Admin' }} · {{ $post->published_at?->format('M j, Y') }}
            · {{ max(1, (int) ceil(str_word_count(strip_tags($post->body)) / 200)) }} min read
        </span>
    </div>
</article>
