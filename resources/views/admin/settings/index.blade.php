@extends('admin.layouts.app')

@section('title', 'Settings')
@section('topbar-title', 'Settings')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Blog Settings</h1>
            <p class="page-sub">General settings that control the public blog.</p>
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

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-field">
                    <label for="site_name">Site Name</label>
                    <input
                        type="text"
                        id="site_name"
                        name="site_name"
                        class="form-input"
                        value="{{ old('site_name', $settings['site_name']) }}"
                        required
                        maxlength="100"
                    >
                </div>

                <div class="form-field">
                    <label for="site_tagline">Tagline <small>(shown under the logo)</small></label>
                    <input
                        type="text"
                        id="site_tagline"
                        name="site_tagline"
                        class="form-input"
                        value="{{ old('site_tagline', $settings['site_tagline']) }}"
                        maxlength="255"
                    >
                </div>

                <div class="form-field">
                    <label for="posts_per_page">Posts Per Page <small>(3–50, used across the blog)</small></label>
                    <input
                        type="number"
                        id="posts_per_page"
                        name="posts_per_page"
                        class="form-input"
                        value="{{ old('posts_per_page', $settings['posts_per_page']) }}"
                        min="3"
                        max="50"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="site_logo">Site Logo <small>(shown in the header — PNG/JPG/SVG/WebP, optional)</small></label>
                    <input type="file" id="site_logo" name="site_logo" class="file-input" accept="image/*">
                    @if (! empty($settings['site_logo']))
                        <div class="image-preview">
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings['site_logo']) }}"
                                alt="Current logo"
                                style="max-height: 70px;"
                            >
                        </div>
                    @endif
                </div>

                <div class="form-field">
                    <label for="site_favicon">Favicon <small>(browser tab icon — ICO/PNG, optional)</small></label>
                    <input type="file" id="site_favicon" name="site_favicon" class="file-input" accept=".ico,.png,.jpg,.jpeg,.svg,.webp">
                    @if (! empty($settings['site_favicon']))
                        <div class="image-preview">
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings['site_favicon']) }}"
                                alt="Current favicon"
                                style="max-height: 32px;"
                            >
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
            </form>
        </div>
    </div>
@endsection
