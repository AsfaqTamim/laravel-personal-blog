<?php

namespace Tests\Feature\Admin;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['email' => 'admin@blog.test']);
    }

    public function test_dashboard_shows_recent_posts_and_pending_comments(): void
    {
        $post = Post::factory()->published()->create(['title' => 'A Short Dashboard Post']);
        $pending = Comment::factory()->pending()->create(['post_id' => $post->id]);

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Recent Posts')
            ->assertSee($post->title)
            ->assertSee('Pending Comments')
            ->assertSee($pending->name)
            ->assertSee('Quick Actions');
    }

    public function test_dashboard_quick_action_links_point_to_admin_pages(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.posts.create'), false)
            ->assertSee(route('admin.categories.create'), false)
            ->assertSee(route('admin.settings.seo'), false);
    }

    public function test_dashboard_shows_empty_states_when_no_content(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('No posts yet.')
            ->assertSee('All caught up');
    }
}
