<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['author', 'category', 'tags'])
                    ->published()
                    ->orderBy('published_at', 'desc');

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->byTag($request->tag);
        }

        // Filter featured posts
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%");
            });
        }

        $posts = $query->paginate($request->get('per_page', 15));

        return response()->json($posts);
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::with(['author', 'category', 'tags', 'approvedComments.user'])
                   ->where('slug', $slug)
                   ->published()
                   ->firstOrFail();

        // Increment view count
        $post->increment('views');

        return response()->json($post);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'author_id' => 'required|exists:users,id',
            'is_featured' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post = Post::create($validated);

        // Attach tags if provided
        if (isset($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        return response()->json($post->load(['author', 'category', 'tags']), 201);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('posts', 'slug')->ignore($post->id)],
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'is_featured' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        // Sync tags if provided
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json($post->load(['author', 'category', 'tags']));
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Get featured posts.
     */
    public function featured(): JsonResponse
    {
        $posts = Post::with(['author', 'category', 'tags'])
                    ->published()
                    ->featured()
                    ->orderBy('published_at', 'desc')
                    ->limit(5)
                    ->get();

        return response()->json($posts);
    }

    /**
     * Get recent posts.
     */
    public function recent(): JsonResponse
    {
        $posts = Post::with(['author', 'category', 'tags'])
                    ->published()
                    ->orderBy('published_at', 'desc')
                    ->limit(10)
                    ->get();

        return response()->json($posts);
    }
}
