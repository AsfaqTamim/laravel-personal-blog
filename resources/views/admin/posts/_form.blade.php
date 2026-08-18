@php
    $isEdit = isset($post) && $post->exists;
    $selectedCategoryId = $isEdit ? $post->categories->first()?->id : null;
    $selectedTags = $isEdit ? $post->tags->pluck('name')->join(', ') : '';
    $existingImageIsUrl = $isEdit && $post->featured_image && Str::startsWith($post->featured_image, 'http');
    $imageUrlValue = old('featured_image_url', $existingImageIsUrl ? $post->featured_image : '');
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@push('styles')
    <link rel="stylesheet" href="{{ asset('summernote/summernote-lite.min.css') }}">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('summernote/summernote-lite.min.js') }}"></script>
    <script>
        $(function () {
            $('#body').summernote({
                height: 320,
                placeholder: 'Write your post here…',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'table', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
            });
        });
    </script>
@endpush

<div class="form-field">
    <label for="title">Title</label>
    <input
        type="text"
        id="title"
        name="title"
        class="form-input"
        value="{{ old('title', $post->title) }}"
        placeholder="My awesome post"
        required
        maxlength="255"
    >
</div>

<div class="form-field">
    <label for="slug">Slug <small>(optional — auto-generated from title)</small></label>
    <input
        type="text"
        id="slug"
        name="slug"
        class="form-input"
        value="{{ old('slug', $post->slug) }}"
        placeholder="my-awesome-post"
        maxlength="255"
    >
</div>

<div class="form-field">
    <label for="excerpt">Excerpt <small>(optional short summary)</small></label>
    <textarea id="excerpt" name="excerpt" class="form-input" rows="3" maxlength="500">{{ old('excerpt', $post->excerpt) }}</textarea>
</div>

<div class="form-field">
    <label for="body">Content</label>
    <textarea id="body" name="body" class="form-input" rows="14" required>{{ old('body', $post->body) }}</textarea>
</div>

<div class="form-field">
    <label for="featured_image">Featured Image <small>(optional — upload a file or paste a URL)</small></label>
    <input type="file" id="featured_image" name="featured_image" class="file-input" accept="image/*">
    <input
        type="url"
        id="featured_image_url"
        name="featured_image_url"
        class="form-input"
        style="margin-top: 10px;"
        value="{{ $imageUrlValue }}"
        placeholder="https://example.com/image.jpg (ignored when a file is uploaded)"
        maxlength="2048"
    >
    @if ($isEdit && $post->featured_image)
        <div class="image-preview">
            <img src="{{ $post->featured_image_url }}" alt="Current featured image">
        </div>
    @endif
</div>

<div class="form-field">
    <label for="category">Category <small>(optional)</small></label>
    @if ($categories->isEmpty())
        <p class="stat-hint">No categories yet — <a href="{{ route('admin.categories.create') }}">create one</a>.</p>
    @else
        <select id="category" name="category" class="form-input">
            <option value="">— No category —</option>
            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((string) old('category', $selectedCategoryId) === (string) $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    @endif
</div>

<div class="form-field">
    <label for="tags">Tags <small>(optional — comma separated, new tags are created automatically)</small></label>
    <input
        type="text"
        id="tags"
        name="tags"
        class="form-input"
        value="{{ old('tags', $selectedTags) }}"
        placeholder="laravel, php, tips"
        maxlength="500"
    >
</div>

<div class="form-field">
    <label class="checkbox">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published))>
        <span>Publish this post</span>
    </label>
</div>

<script>
    (function () {
        var title = document.getElementById('title');
        var slug = document.getElementById('slug');
        var slugTouched = false;

        if (slug && slug.value) {
            slugTouched = true;
        }

        if (title && slug) {
            title.addEventListener('input', function () {
                if (!slugTouched) {
                    slug.value = title.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            });

            slug.addEventListener('input', function () {
                slugTouched = slug.value !== '';
            });
        }
    })();
</script>
