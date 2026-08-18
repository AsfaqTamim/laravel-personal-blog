<?php

namespace Tests\Feature\Blog;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_only_published_posts(): void
    {
        $published = Post::factory()->published()->create(['user_id' => User::factory()]);
        $draft = Post::factory()->draft()->create(['user_id' => User::factory()]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_post_page_shows_approved_comments_only(): void
    {
        $post = Post::factory()->published()->create(['user_id' => User::factory()]);

        $approved = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $pending = Comment::factory()->pending()->create(['post_id' => $post->id]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($approved->body)
            ->assertDontSee($pending->body);
    }

    public function test_draft_post_returns_404(): void
    {
        $draft = Post::factory()->draft()->create(['user_id' => User::factory()]);

        $this->get(route('blog.show', $draft))->assertNotFound();
    }

    public function test_visitor_can_submit_a_comment(): void
    {
        $post = Post::factory()->published()->create(['user_id' => User::factory()]);

        $this->post(route('blog.comments.store', $post), [
            'name' => 'Jane Reader',
            'email' => 'jane@example.com',
            'body' => 'Great article, thanks!',
        ])->assertRedirect(route('blog.show', $post));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'name' => 'Jane Reader',
            'is_approved' => false,
        ]);
    }

    public function test_category_page_lists_its_posts(): void
    {
        $category = Category::factory()->create();
        $other = Category::factory()->create();

        $inCategory = Post::factory()->published()->create(['user_id' => User::factory()]);
        $notInCategory = Post::factory()->draft()->create(['user_id' => User::factory()]);

        $inCategory->categories()->attach($category);
        $notInCategory->categories()->attach($other);

        $this->get(route('blog.category', $category))
            ->assertOk()
            ->assertSee($inCategory->title)
            ->assertDontSee($notInCategory->title);
    }

    public function test_search_finds_published_posts(): void
    {
        $hit = Post::factory()->published()->create([
            'title' => 'How to Deploy Laravel',
            'user_id' => User::factory(),
        ]);
        $miss = Post::factory()->draft()->create([
            'title' => 'Cooking Pasta',
            'user_id' => User::factory(),
        ]);

        $this->get(route('blog.search', ['q' => 'deploy']))
            ->assertOk()
            ->assertSee($hit->title)
            ->assertDontSee($miss->title);
    }

    public function test_rss_feed_lists_latest_posts(): void
    {
        $post = Post::factory()->published()->create(['user_id' => User::factory()]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertHeader('content-type', 'application/rss+xml; charset=UTF-8')
            ->assertSee($post->title);
    }

    public function test_blog_page_lists_published_posts(): void
    {
        $published = Post::factory()->published()->create(['user_id' => User::factory()]);
        $draft = Post::factory()->draft()->create(['user_id' => User::factory()]);

        $this->get(route('blog.all'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_categories_page_lists_categories(): void
    {
        $category = Category::factory()->create();

        $this->get(route('blog.categories'))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_html_body_from_summernote_is_rendered(): void
    {
        $post = Post::factory()->published()->create([
            'user_id' => User::factory(),
            'body' => '<h2>Rich Heading</h2><p>Hello <b>bold</b> and <a href="https://laravel.com">a link</a>.</p>',
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<h2>Rich Heading</h2>', false)
            ->assertSee('<b>bold</b>', false)
            ->assertSee('https://laravel.com', false);
    }

    public function test_post_pages_have_full_seo_meta(): void
    {
        $post = Post::factory()->published()->create([
            'user_id' => User::factory(),
            'excerpt' => 'A short SEO description for this article.',
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<meta name="description" content="A short SEO description for this article.">', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta name="twitter:card"', false)
            ->assertSee('"@type": "BlogPosting"', false)
            ->assertSee('"headline"', false)
            ->assertSee('"datePublished"', false);
    }

    public function test_search_pages_are_noindex(): void
    {
        $this->get(route('blog.search', ['q' => 'laravel']))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_sitemap_lists_posts_and_categories(): void
    {
        $post = Post::factory()->published()->create(['user_id' => User::factory()]);
        $category = Category::factory()->create();

        $this->get(route('blog.sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee(route('blog.show', $post))
            ->assertSee(route('blog.category', $category));
    }

    public function test_robots_txt_points_to_sitemap(): void
    {
        $this->get(route('blog.robots'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_ajax_load_more_returns_only_card_partial(): void
    {
        $user = User::factory()->create();

        $posts = collect();
        for ($i = 0; $i < 8; $i++) {
            $posts->push(Post::factory()->published()->create([
                'user_id' => $user->id,
                'published_at' => now()->subDays(8 - $i),
            ]));
        }

        // Newest post (subDays(1)) is on page 1, oldest (subDays(8)) on page 2.
        $this->get(route('blog.index', ['page' => 2]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertViewIs('blog._cards')
            ->assertSee($posts->first()->title)
            ->assertDontSee($posts->last()->title);
    }
}
