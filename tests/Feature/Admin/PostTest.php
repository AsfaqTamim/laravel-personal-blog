<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_guests_cannot_access_posts(): void
    {
        $this->get(route('admin.posts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_posts_index(): void
    {
        $admin = $this->admin();

        Post::factory()->count(3)->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Posts');
    }

    public function test_admin_can_create_a_post(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.posts.store'), [
                'title' => 'Hello World',
                'body' => 'Some content for the post.',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'is_published' => true,
        ]);
    }

    public function test_post_title_is_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.posts.store'), [
                'title' => '',
                'body' => 'Body without a title.',
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_publish_a_draft(): void
    {
        $admin = $this->admin();

        $post = Post::factory()->draft()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $post), [
                'title' => 'Updated Title',
                'body' => 'Updated body.',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'is_published' => true,
        ]);
    }

    public function test_admin_can_assign_a_category_when_creating_a_post(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.posts.store'), [
                'title' => 'Categorized Post',
                'body' => 'Content with a category.',
                'category' => $category->id,
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::where('title', 'Categorized Post')->firstOrFail();

        $this->assertTrue($post->categories()->whereKey($category->id)->exists());
    }

    public function test_admin_can_upload_a_featured_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('cover.jpg', 400, 300);

        $this->actingAs($this->admin())
            ->post(route('admin.posts.store'), [
                'title' => 'Post with image',
                'body' => 'Content',
                'featured_image' => $file,
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::where('title', 'Post with image')->firstOrFail();

        $this->assertNotNull($post->featured_image);
        Storage::disk('public')->assertExists($post->featured_image);
    }

    public function test_admin_can_delete_a_post(): void
    {
        $admin = $this->admin();

        $post = Post::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->delete(route('admin.posts.destroy', $post))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
