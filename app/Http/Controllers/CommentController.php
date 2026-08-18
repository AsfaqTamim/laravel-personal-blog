<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment on a published post (public).
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_if(! $post->is_published, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create([
            ...$data,
            'is_approved' => false,
        ]);

        return redirect()
            ->route('blog.show', $post)
            ->with('comment_status', 'Thanks! Your comment has been submitted and is awaiting moderation.');
    }
}
