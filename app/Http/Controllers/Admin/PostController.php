<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * List all posts.
     */
    public function index(Request $request): View
    {
        $query = Post::with(['user', 'categories'])->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a post.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.create', ['post' => new Post(), 'categories' => $categories]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title'], $data['slug'] ?? null);
        $data['published_at'] = $data['is_published'] ? now() : null;

        $post = Post::create($data);
        $post->categories()->sync($request->filled('category') ? [$request->input('category')] : []);
        $post->tags()->sync($this->tagIds((string) $request->input('tags', '')));

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();
        $post->load('tags');

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the given post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validatedData($request, $post);

        $data['slug'] = $this->uniqueSlug($data['title'], $data['slug'] ?? null, $post->id);
        $data['published_at'] = $data['is_published']
            ? ($post->published_at ?? now())
            : null;

        $oldImage = $post->featured_image;

        $post->update($data);
        $post->categories()->sync($request->filled('category') ? [$request->input('category')] : []);
        $post->tags()->sync($this->tagIds((string) $request->input('tags', '')));

        // Clean up the old stored image when it was replaced
        if ($oldImage
            && ($data['featured_image'] ?? null) !== $oldImage
            && ! Str::startsWith($oldImage, ['http://', 'https://'])
        ) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Delete the given post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        // Clean up a stored featured image
        if ($post->featured_image && ! Str::startsWith($post->featured_image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Validate the post form data.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Post $post = null): array
    {
        $slugRule = Rule::unique('posts', 'slug');

        if ($post) {
            $slugRule->ignore($post->id);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'featured_image_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $request->validate([
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        // An uploaded file wins over a pasted URL
        $data['featured_image'] = $request->hasFile('featured_image')
            ? $request->file('featured_image')->store('posts', 'public')
            : $request->input('featured_image_url');

        return $data;
    }

    /**
     * Turn a comma-separated tag list into tag ids,
     * creating any new tags on the fly.
     *
     * @return array<int, int>
     */
    private function tagIds(string $tags): array
    {
        $names = collect(explode(',', $tags))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->map(fn ($name) => mb_substr($name, 0, 100))
            ->unique()
            ->values();

        return $names->map(function (string $name) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );

            return $tag->id;
        })->all();
    }

    /**
     * Generate a unique slug.
     */
    private function uniqueSlug(string $title, ?string $requestedSlug = null, ?int $ignoreId = null): string
    {
        $slug = $requestedSlug ? Str::slug($requestedSlug) : Str::slug($title);

        if ($slug === '') {
            $slug = Str::lower(Str::random(8));
        }

        $original = $slug;
        $counter = 2;

        while (Post::query()
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
