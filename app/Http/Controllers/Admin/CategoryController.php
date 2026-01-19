<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        
        $query = Category::withCount('products');
        
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }
        
        $categories = $query->paginate(15);
        
        return response()->json($categories);
    }

    /**
     * Get all active categories (for dropdowns/selects).
     */
    public function listAll()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($categories);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Category::generateSlug($validated['name']);
        
        $category = Category::create($validated);
        
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();
        $validated['slug'] = Category::generateSlug($validated['name']);
        
        $category->update($validated);
        
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified category from storage (soft delete).
     */
    public function destroy(Category $category)
    {
        // Check if category has active products
        if ($category->hasActiveProducts()) {
            return response()->json([
                'message' => 'Cannot delete category with active products',
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Restore a soft-deleted category.
     */
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        
        return response()->json([
            'message' => 'Category restored successfully',
            'data' => $category,
        ]);
    }

    /**
     * Permanently delete a category (force delete).
     */
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        
        return response()->json([
            'message' => 'Category permanently deleted',
        ]);
    }
}
