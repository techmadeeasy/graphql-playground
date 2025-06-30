<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display comments for a specific post.
     */
    public function index(string $postSlug): JsonResponse
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();
        
        $comments = Comment::with(['user', 'approvedReplies.user'])
                          ->where('post_id', $post->id)
                          ->approved()
                          ->topLevel()
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json($comments);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'nullable|exists:users,id',
            'author_name' => 'required_without:user_id|string|max:255',
            'author_email' => 'required_without:user_id|email|max:255',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create($validated);

        return response()->json($comment->load(['user']), 201);
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user', 'post', 'approvedReplies.user']);
        
        return response()->json($comment);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'status' => 'sometimes|in:pending,approved,rejected',
        ]);

        $comment->update($validated);

        return response()->json($comment->load(['user']));
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment): JsonResponse
    {
        $comment->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Comment approved successfully',
            'comment' => $comment->load(['user'])
        ]);
    }

    /**
     * Reject a comment.
     */
    public function reject(Comment $comment): JsonResponse
    {
        $comment->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Comment rejected successfully',
            'comment' => $comment->load(['user'])
        ]);
    }

    /**
     * Get pending comments (for moderation).
     */
    public function pending(): JsonResponse
    {
        $comments = Comment::with(['user', 'post'])
                          ->pending()
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        return response()->json($comments);
    }

    /**
     * Get recent comments.
     */
    public function recent(): JsonResponse
    {
        $comments = Comment::with(['user', 'post'])
                          ->approved()
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();

        return response()->json($comments);
    }
}
