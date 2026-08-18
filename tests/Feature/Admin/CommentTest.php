<?php

namespace Tests\Feature\Admin;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_admin_can_view_comments_index(): void
    {
        Comment::factory()->pending()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.comments.index'))
            ->assertOk()
            ->assertSee('Comments');
    }

    public function test_admin_can_approve_a_comment(): void
    {
        $comment = Comment::factory()->pending()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.comments.approve', $comment))
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'is_approved' => true,
        ]);
    }

    public function test_admin_can_unapprove_a_comment(): void
    {
        $comment = Comment::factory()->approved()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.comments.unapprove', $comment))
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_delete_a_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.comments.destroy', $comment))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
