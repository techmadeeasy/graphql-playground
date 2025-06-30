<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::active()
                            ->withCount(['publishedPosts'])
                            ->orderBy('name')
                            ->get();

        return response()->json($categories);
    }

    /**
     * Display the specified category.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
                          ->active()
                          ->withCount(['publishedPosts'])
                          ->firstOrFail();

        return response()->json($category);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7', // For hex colors
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Get posts for a specific category.
     */
    public function posts(string $slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        
        $posts = $category->publishedPosts()
                         ->with(['author', 'tags'])
                         ->orderBy('published_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
