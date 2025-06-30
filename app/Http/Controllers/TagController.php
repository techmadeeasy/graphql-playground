<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(): JsonResponse
    {
        $tags = Tag::withCount(['publishedPosts'])
                  ->orderBy('name')
                  ->get();

        return response()->json($tags);
    }

    /**
     * Display the specified tag.
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)
                 ->withCount(['publishedPosts'])
                 ->firstOrFail();

        return response()->json($tag);
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:tags,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7', // For hex colors
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag = Tag::create($validated);

        return response()->json($tag, 201);
    }

    /**
     * Update the specified tag.
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('tags', 'slug')->ignore($tag->id)],
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag->update($validated);

        return response()->json($tag);
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    /**
     * Get posts for a specific tag.
     */
    public function posts(string $slug, Request $request): JsonResponse
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $posts = $tag->publishedPosts()
                    ->with(['author', 'category'])
                    ->orderBy('published_at', 'desc')
                    ->paginate($request->get('per_page', 15));

        return response()->json([
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }

    /**
     * Get popular tags (tags with most posts).
     */
    public function popular(): JsonResponse
    {
        $tags = Tag::withCount(['publishedPosts'])
                  ->orderBy('published_posts_count', 'desc')
                  ->limit(20)
                  ->get();

        return response()->json($tags);
    }
}
