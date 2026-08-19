<!DOCTYPE html>
@php($siteName = blog_setting('site_name', config('app.name', 'Clara Dawson Blogs')))
@php($siteTagline = blog_setting('site_tagline', 'Stories, ideas & insights on all kinds of topics'))
@php($pageTitle = trim((string) $__env->yieldContent('title')))
@php($seoTitle = $pageTitle !== '' ? $pageTitle.' — '.$siteName : $siteName.' — '.$siteTagline)
@php($seoDescription = trim((string) $__env->yieldContent('description')) ?: (blog_setting('seo_description') ?: $siteTagline))
@php($seoKeywords = trim((string) blog_setting('seo_keywords')))
@php($siteAuthor = trim((string) blog_setting('seo_author')) ?: 'Clara Dawson')
@php($ogImage = trim((string) $__env->yieldContent('og_image')) ?: trim((string) blog_setting('seo_og_image')))
@php($noindex = trim((string) $__env->yieldContent('noindex')) === '1')
@php($defaultRobots = trim((string) blog_setting('seo_robots')) ?: 'index, follow')
@php($googleVerification = trim((string) blog_setting('google_site_verification')))
@php($bingVerification = trim((string) blog_setting('bing_site_verification')))
@php($siteLogo = trim((string) blog_setting('site_logo')))
@php($siteFavicon = trim((string) blog_setting('site_favicon')))
@php($canonical = url()->current())
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle }}</title>
    @if ($siteFavicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($siteFavicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <meta name="description" content="{{ $seoDescription }}">
    @if ($noindex)
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="{{ $defaultRobots }}">
    @endif
    @if ($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="author" content="{{ $siteAuthor }}">
    <link rel="canonical" href="{{ $canonical }}">

    @if ($googleVerification)
        <meta name="google-site-verification" content="{{ $googleVerification }}">
    @endif
    @if ($bingVerification)
        <meta name="msvalidate.01" content="{{ $bingVerification }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $pageTitle !== '' ? 'article' : 'website' }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@400;500;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blog-modern.css') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $siteName }} — RSS Feed" href="{{ route('blog.feed') }}">

    @yield('structured_data')

    {{-- Structured data: WebSite + search box --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": {!! json_ld($siteName) !!},
        "description": {!! json_ld($siteTagline) !!},
        "url": {!! json_ld(url('/')) !!},
        "potentialAction": {
            "@@type": "SearchAction",
            "target": {!! json_ld(route('blog.search').'?q={search_term_string}') !!},
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="masthead-top">
                <span><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> {{ now()->format('l, F j, Y') }}</span>
                <span class="masthead-tagline"><i class="fa-solid fa-feather-pointed" aria-hidden="true"></i> {{ $siteTagline }}</span>
                <a href="{{ route('blog.feed') }}" class="masthead-rss"><i class="fa-solid fa-rss" aria-hidden="true"></i> RSS Feed</a>
            </div>

            <div class="masthead-row">
                <a href="{{ route('blog.index') }}" class="masthead-brand">
                    @if ($siteLogo)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogo) }}"
                            alt="{{ $siteName }}"
                            class="masthead-logo"
                        >
                    @else
                        {{ $siteName }}
                    @endif
                </a>

                <button type="button" class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="main-nav">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>

                <nav class="masthead-nav" id="main-nav">
                    <form class="site-search" method="GET" action="{{ route('blog.search') }}" role="search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <input type="search" name="q" placeholder="Search posts…" value="{{ request('q') }}" aria-label="Search">
                    </form>
                    <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.index') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('blog.all') }}" class="nav-link {{ request()->routeIs('blog.all') ? 'active' : '' }}">Blog</a>
                    <a href="{{ route('blog.categories') }}" class="nav-link {{ request()->routeIs('blog.categories') ? 'active' : '' }}">Categories</a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">Log in</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="paper-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="brand-row">
                        @if ($siteLogo)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogo) }}"
                                alt="{{ $siteName }}"
                                class="footer-logo"
                            >
                        @else
                            <span class="brand-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span>
                        @endif
                        {{ $siteName }}
                    </div>
                    <p>Articles, stories and insights on all kinds of topics — tech, life and everything in between. Written by humans, for humans.</p>
                </div>

                <div class="footer-col">
                    <h4>Explore</h4>
                    <ul>
                        <li><a href="{{ route('blog.index') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a></li>
                        <li><a href="{{ route('blog.all') }}"><i class="fa-solid fa-newspaper" aria-hidden="true"></i> Blog</a></li>
                        <li><a href="{{ route('blog.categories') }}"><i class="fa-solid fa-tags" aria-hidden="true"></i> Categories</a></li>
                        <li><a href="{{ route('blog.search') }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Search</a></li>
                        <li><a href="{{ route('blog.feed') }}"><i class="fa-solid fa-rss" aria-hidden="true"></i> RSS Feed</a></li>
                        @auth
                            <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}"><i class="fa-solid fa-lock" aria-hidden="true"></i> Admin Login</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Categories</h4>
                    <ul>
                        @forelse (\App\Models\Category::orderBy('name')->limit(6)->get() as $category)
                            <li><a href="{{ route('blog.category', $category) }}">{{ $category->name }}</a></li>
                        @empty
                            <li><a href="{{ route('blog.index') }}">No categories yet</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
                <span>Built with <a href="https://laravel.com" target="_blank" rel="noopener">Laravel</a> · <a class="rss" href="{{ route('blog.feed') }}"><i class="fa-solid fa-rss" aria-hidden="true"></i>RSS</a></span>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/blog.js') }}" defer></script>
</body>
</html>
