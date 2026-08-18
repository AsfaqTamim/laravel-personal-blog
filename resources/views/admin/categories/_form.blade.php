@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-field">
    <label for="name">Name</label>
    <input
        type="text"
        id="name"
        name="name"
        class="form-input"
        value="{{ old('name', $category->name) }}"
        placeholder="Laravel"
        required
        maxlength="255"
    >
</div>

<div class="form-field">
    <label for="slug">Slug <small>(optional — auto-generated from name)</small></label>
    <input
        type="text"
        id="slug"
        name="slug"
        class="form-input"
        value="{{ old('slug', $category->slug) }}"
        placeholder="laravel"
        maxlength="255"
    >
</div>

<script>
    (function () {
        var name = document.getElementById('name');
        var slug = document.getElementById('slug');
        var slugTouched = false;

        if (slug && slug.value) {
            slugTouched = true;
        }

        if (name && slug) {
            name.addEventListener('input', function () {
                if (!slugTouched) {
                    slug.value = name.value
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
