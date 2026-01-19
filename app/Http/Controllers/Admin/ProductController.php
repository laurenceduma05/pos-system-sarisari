<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        $category = $request->query('category');
        $lowStock = $request->boolean('low_stock', false);
        $search = $request->query('search');
        
        $query = Product::with('category');
        
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }
        
        if ($category) {
            $query = $query->where('category_id', $category);
        }
        
        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        if ($lowStock) {
            $query = $query->whereRaw('stock <= min_stock');
        }
        
        $products = $query->paginate(15);
        
        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Product::generateSlug($validated['name']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $validated['image'] = $path;
        }
        
        $product = Product::create($validated);
        $product->load('category');
        
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $validated['slug'] = Product::generateSlug($validated['name']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $validated['image'] = $path;
        }
        
        $product->update($validated);
        $product->load('category');
        
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified product from storage (soft delete).
     */
    public function destroy(Product $product)
    {
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        $product->load('category');
        
        return response()->json([
            'message' => 'Product restored successfully',
            'data' => $product,
        ]);
    }

    /**
     * Permanently delete a product (force delete).
     */
    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        
        // Delete image file
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->forceDelete();
        
        return response()->json([
            'message' => 'Product permanently deleted',
        ]);
    }

    /**
     * Get low stock products.
     */
    public function lowStock(Request $request)
    {
        $products = Product::whereRaw('stock <= min_stock')
            ->where('is_active', true)
            ->with('category')
            ->paginate(15);
        
        return response()->json($products);
    }

    /**
     * Search products by name, SKU, or barcode.
     */
    public function search(Request $request)
    {
        $search = $request->query('q');
        
        if (!$search || strlen($search) < 2) {
            return response()->json([
                'message' => 'Search term must be at least 2 characters',
            ], 422);
        }
        
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->with('category')
            ->limit(20)
            ->get();
        
        return response()->json($products);
    }
}
