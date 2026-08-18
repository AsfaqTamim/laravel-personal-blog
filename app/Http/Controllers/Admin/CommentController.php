<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * List all comments (filterable by status).
     */
    public function index(Request $request): View
    {
        $query = Comment::with('post')->latest();

        if ($request->query('status') === 'pending') {
            $query->pending();
        } elseif ($request->query('status') === 'approved') {
            $query->approved();
        }

        $comments = $query->paginate(15)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Approve the given comment.
     */
    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => true]);

        return back()->with('success', 'Comment approved.');
    }

    /**
     * Unapprove the given comment.
     */
    public function unapprove(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => false]);

        return back()->with('success', 'Comment unapproved.');
    }

    /**
     * Delete the given comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
