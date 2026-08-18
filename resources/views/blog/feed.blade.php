<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ config('app.name', 'DML Blog') }}</title>
    <link>{{ route('blog.index') }}</link>
    <description>Latest articles from {{ config('app.name', 'DML Blog') }}</description>
    <language>en</language>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
    <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />

    @foreach ($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('blog.show', $post) }}</link>
            <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            <description><![CDATA[{{ Str::limit($post->excerpt ?: strip_tags($post->body), 300) }}]]></description>
            @foreach ($post->categories as $category)
                <category>{{ $category->name }}</category>
            @endforeach
        </item>
    @endforeach
</channel>
</rss>
