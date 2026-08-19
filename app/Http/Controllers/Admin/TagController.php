<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * List all tags.
     */
    public function index(): View
    {
        $tags = Tag::withCount('posts')->latest()->paginate(10);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a tag.
     */
    public function create(): View
    {
        return view('admin.tags.create', ['tag' => new Tag()]);
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['slug'] = $this->uniqueSlug($data['name'], $data['slug'] ?? null);

        Tag::create($data);

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Show the form for editing a tag.
     */
    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the given tag.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $this->validatedData($request, $tag);

        $data['slug'] = $this->uniqueSlug($data['name'], $data['slug'] ?? null, $tag->id);

        $tag->update($data);

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Delete the given tag.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }

    /**
     * Validate the tag form data.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Tag $tag = null): array
    {
        $slugRule = Rule::unique('tags', 'slug');

        if ($tag) {
            $slugRule->ignore($tag->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
        ]);
    }

    /**
     * Generate a unique slug.
     */
    private function uniqueSlug(string $name, ?string $requestedSlug = null, ?int $ignoreId = null): string
    {
        $slug = $requestedSlug ? Str::slug($requestedSlug) : Str::slug($name);

        if ($slug === '') {
            $slug = Str::lower(Str::random(8));
        }

        $original = $slug;
        $counter = 2;

        while (Tag::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
