@extends('admin.layouts.app')

@section('title', 'SEO Settings')
@section('topbar-title', 'Settings')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">SEO Settings</h1>
            <p class="page-sub">Control how the blog appears in search engines and on social media.</p>
        </div>
    </div>

    <div class="filter-tabs" style="margin-bottom: 22px;">
        <a href="{{ route('admin.settings.index') }}" class="filter-tab {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
            <i class="fa-solid fa-sliders" aria-hidden="true"></i> General
        </a>
        <a href="{{ route('admin.settings.seo') }}" class="filter-tab {{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
            <i class="fa-solid fa-magnifying-glass-chart" aria-hidden="true"></i> SEO
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel" style="max-width: 640px;">
        <div class="panel-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.seo.update') }}">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="seo_description">Default Meta Description <small>(used when a page has no description)</small></label>
                    <textarea id="seo_description" name="seo_description" class="form-input" rows="3" maxlength="255">{{ old('seo_description', $settings['seo_description']) }}</textarea>
                </div>

                <div class="form-field">
                    <label for="seo_keywords">Meta Keywords <small>(comma separated, optional)</small></label>
                    <input
                        type="text"
                        id="seo_keywords"
                        name="seo_keywords"
                        class="form-input"
                        value="{{ old('seo_keywords', $settings['seo_keywords']) }}"
                        placeholder="blog, laravel, stories"
                        maxlength="255"
                    >
                </div>

                <div class="form-field">
                    <label for="seo_og_image">Default Share Image URL <small>(used when a post has no featured image)</small></label>
                    <input
                        type="url"
                        id="seo_og_image"
                        name="seo_og_image"
                        class="form-input"
                        value="{{ old('seo_og_image', $settings['seo_og_image']) }}"
                        placeholder="https://yourdomain.com/images/og.jpg"
                        maxlength="2048"
                    >
                </div>

                <div class="form-field">
                    <label for="seo_author">Author Name <small>(shown in meta tags)</small></label>
                    <input
                        type="text"
                        id="seo_author"
                        name="seo_author"
                        class="form-input"
                        value="{{ old('seo_author', $settings['seo_author']) }}"
                        maxlength="100"
                    >
                </div>

                <div class="form-field">
                    <label for="seo_robots">Default Robots Directive</label>
                    <select id="seo_robots" name="seo_robots" class="form-input">
                        @foreach (['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow'] as $robots)
                            <option value="{{ $robots }}" @selected(old('seo_robots', $settings['seo_robots']) === $robots)>{{ $robots }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="google_site_verification">Google Search Console <small>(content of the verification meta tag)</small></label>
                    <input
                        type="text"
                        id="google_site_verification"
                        name="google_site_verification"
                        class="form-input"
                        value="{{ old('google_site_verification', $settings['google_site_verification']) }}"
                        placeholder="e.g. AbC123xYz..."
                        maxlength="255"
                    >
                </div>

                <div class="form-field">
                    <label for="bing_site_verification">Bing Webmaster <small>(content of the msvalidate meta tag)</small></label>
                    <input
                        type="text"
                        id="bing_site_verification"
                        name="bing_site_verification"
                        class="form-input"
                        value="{{ old('bing_site_verification', $settings['bing_site_verification']) }}"
                        placeholder="e.g. 1A2B3C4D5E..."
                        maxlength="255"
                    >
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save SEO Settings</button>
            </form>
        </div>
    </div>
@endsection
