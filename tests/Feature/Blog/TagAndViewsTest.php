<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagAndViewsTest extends TestCase
{
    use RefreshDatabase;

    private function humanUserAgent(): string
    {
        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';
    }

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@dml-blog.test']);
    }

    public function test_post_views_increment_for_human_visitors(): void
    {
        $post = Post::factory()->published()->create(['views_count' => 10]);

        $this->withHeaders(['User-Agent' => $this->humanUserAgent()])
            ->get(route('blog.show', $post))
            ->assertOk();

        $this->assertSame(11, $post->fresh()->views_count);
    }

    public function test_bot_views_are_not_counted(): void
    {
        $post = Post::factory()->published()->create(['views_count' => 5]);

        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
            ->get(route('blog.show', $post))
            ->assertOk();

        $this->assertSame(5, $post->fresh()->views_count);
    }

    public function test_post_page_shows_view_count_and_tags(): void
    {
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $post = Post::factory()->published()->create(['views_count' => 42]);
        $post->tags()->attach($tag);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee(number_format($post->fresh()->views_count).' views')
            ->assertSee('Laravel');
    }

    public function test_tag_page_lists_tagged_posts(): void
    {
        $tag = Tag::factory()->create();
        $tagged = Post::factory()->published()->create();
        $untagged = Post::factory()->published()->create();
        $tagged->tags()->attach($tag);

        $this->get(route('blog.tag', $tag))
            ->assertOk()
            ->assertSee($tagged->title)
            ->assertDontSee($untagged->title);
    }

    public function test_homepage_shows_most_read_section(): void
    {
        $low = Post::factory()->published()->create(['views_count' => 1]);
        $high = Post::factory()->published()->create(['views_count' => 500]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Most Read')
            ->assertSee($high->title)
            ->assertSee('500 views');
    }

    public function test_admin_can_create_post_with_tags_that_auto_create(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.posts.store'), [
                'title' => 'Tagged Post',
                'body' => 'Some body text.',
                'is_published' => '1',
                'tags' => 'Laravel, PHP, laravel',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::where('title', 'Tagged Post')->firstOrFail();

        $this->assertSame(2, $post->tags()->count());
        $this->assertSame(['Laravel', 'PHP'], $post->tags()->orderBy('name')->pluck('name')->all());
    }

    public function test_admin_can_update_post_tags(): void
    {
        $post = Post::factory()->published()->create();
        $post->tags()->attach(Tag::factory()->create(['name' => 'Old Tag']));

        $this->actingAs($this->admin())
            ->put(route('admin.posts.update', $post), [
                'title' => $post->title,
                'body' => $post->body,
                'is_published' => '1',
                'tags' => 'New Tag',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSame(['New Tag'], $post->fresh()->tags()->pluck('name')->all());
    }

    public function test_admin_posts_table_shows_view_counts(): void
    {
        Post::factory()->published()->create(['title' => 'Viewed Post', 'views_count' => 1234]);

        $this->actingAs($this->admin())
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('1,234');
    }

    public function test_admin_can_manage_tags(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.tags.store'), ['name' => 'DevOps'])
            ->assertRedirect(route('admin.tags.index'));

        $tag = Tag::where('slug', 'devops')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.tags.update', $tag), ['name' => 'Dev Ops'])
            ->assertRedirect(route('admin.tags.index'));

        $this->assertSame('Dev Ops', $tag->fresh()->name);

        $this->actingAs($admin)
            ->delete(route('admin.tags.destroy', $tag))
            ->assertRedirect(route('admin.tags.index'));

        $this->assertSame(0, Tag::count());
    }
}
