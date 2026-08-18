<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Homepage — latest published posts.
     */
    public function index(Request $request): View
    {
        $posts = Post::with(['user', 'categories'])
            ->published()
            ->latest('published_at')
            ->paginate((int) blog_setting('posts_per_page', 6));

        // Newspaper-style category sections: every category with its 3 latest posts
        $sections = Category::with([
            'posts' => fn ($query) => $query->published()->latest('published_at')->limit(3),
        ])
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->posts->isNotEmpty());

        $allCategories = Category::withCount([
            'posts as published_count' => fn ($query) => $query->published(),
        ])->orderBy('name')->get();

        // Most read articles across the whole blog.
        $mostRead = Post::with('categories')
            ->published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        if ($request->ajax()) {
            return view('blog._cards', compact('posts'));
        }

        return view('blog.index', compact('posts', 'sections', 'allCategories', 'mostRead'));
    }

    /**
     * All published posts (the "Blog" page).
     */
    public function all(Request $request): View
    {
        $posts = Post::with(['user', 'categories'])
            ->published()
            ->latest('published_at')
            ->paginate((int) blog_setting('posts_per_page', 9));

        if ($request->ajax()) {
            return view('blog._cards', compact('posts'));
        }

        return view('blog.all', compact('posts'));
    }

    /**
     * All categories.
     */
    public function categories(): View
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();

        return view('blog.categories', compact('categories'));
    }

    /**
     * Search published posts.
     */
    public function search(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $posts = Post::with(['user', 'categories'])
            ->published()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($builder) use ($q) {
                    $builder->where('title', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%")
                        ->orWhere('body', 'like', "%{$q}%");
                });
            })
            ->latest('published_at')
            ->paginate((int) blog_setting('posts_per_page', 9))
            ->withQueryString();

        if ($request->ajax()) {
            return view('blog._cards', compact('posts'));
        }

        return view('blog.search', compact('posts', 'q'));
    }

    /**
     * A single published post.
     */
    public function show(Request $request, Post $post): View
    {
        abort_if(! $post->is_published, 404);

        $post->load([
            'user',
            'categories',
            'tags',
            'comments' => fn ($query) => $query->approved()->oldest(),
        ]);

        // Count the view — but never count bots or crawlers.
        if (! Visit::isBot($request->userAgent())) {
            $post->increment('views_count');
        }

        return view('blog.show', compact('post'));
    }

    /**
     * Posts of a single category.
     */
    public function category(Request $request, Category $category): View
    {
        $posts = $category->posts()
            ->with(['user', 'categories'])
            ->published()
            ->latest('published_at')
            ->paginate((int) blog_setting('posts_per_page', 6));

        if ($request->ajax()) {
            return view('blog._cards', compact('posts'));
        }

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Posts carrying a single tag.
     */
    public function tag(Request $request, Tag $tag): View
    {
        $posts = $tag->posts()
            ->with(['user', 'categories'])
            ->published()
            ->latest('published_at')
            ->paginate((int) blog_setting('posts_per_page', 6));

        if ($request->ajax()) {
            return view('blog._cards', compact('posts'));
        }

        return view('blog.tag', compact('tag', 'posts'));
    }

    /**
     * RSS feed of the latest published posts.
     */
    public function feed(): Response
    {
        $posts = Post::with(['user', 'categories'])
            ->published()
            ->latest('published_at')
            ->take(20)
            ->get();

        return response()
            ->view('blog.feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    /**
     * XML sitemap for search engines.
     */
    public function sitemap(): Response
    {
        $posts = Post::published()->get(['slug', 'updated_at']);
        $categories = Category::all(['slug', 'updated_at']);
        $tags = Tag::all(['slug', 'updated_at']);

        return response()
            ->view('blog.sitemap', compact('posts', 'categories', 'tags'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * robots.txt pointing to the sitemap.
     */
    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /admin\n\n";
        $content .= 'Sitemap: '.url('/sitemap.xml')."\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
