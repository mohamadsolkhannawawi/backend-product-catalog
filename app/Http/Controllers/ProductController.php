<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * List produk seller
     */
    public function index(Request $request)
    {
        $seller = $request->user()->seller;

        if (!$seller) {
            return response()->json([
                'message' => 'Seller account not found.',
            ], 404);
        }

        $products = $seller->products()
            ->select('product_id', 'name', 'slug', 'price', 'stock', 'images', 'category_id', 'is_active', 'created_at')
            ->with('category:category_id,name')
            ->orderBy('product_id', 'desc')
            ->get();

        return response()->json($products);
    }

    /**
     * Create produk
     */
    public function store(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'category_id' => 'nullable|uuid|exists:categories,category_id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'status'      => 'nullable|in:draft,active,inactive,discontinued',

            // Enforce at least 2 images per SRS (primary + secondary)
            'images'      => 'required|array|min:2',
            'images.*'    => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Slug otomatis
        $slug = Str::slug($validated['name']) . '-' . uniqid();

        // Upload gambar (jika ada)
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {

                $filename = 'product_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');

                $imagePaths[] = '/storage/' . $path;
            }
        }

        $product = Product::create([
            'seller_id'   => $seller->seller_id,
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'stock'       => $validated['stock'],
            'status'      => $validated['status'] ?? 'draft',
            'category'    => $validated['category'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'images'      => $imagePaths,
            'primary_image' => count($imagePaths) ? $imagePaths[0] : null,
        ]);

        // Bump cache version so public listings reflect new product
        try { Cache::increment('products_cache_version'); } catch (\Exception $e) { Cache::put('products_cache_version', 1); }

        return response()->json([
            'message' => 'Product created',
            'product' => $product,
        ], 201);
    }

    /**
     * Show detail produk
     */
    public function show(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        if ($product->seller_id !== $seller->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Load category relationship
        $product->load('category:category_id,name');

        return response()->json($product);
    }

    /**
     * Update produk
     * POST form-data untuk file upload
     */
    public function update(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        if ($product->seller_id !== $seller->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'category_id' => 'nullable|uuid|exists:categories,category_id',
            'price'       => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'status'      => 'nullable|in:draft,active,inactive,discontinued',
            // If images are provided on update, enforce min 2 to satisfy SRS
            'images'      => 'nullable|array|min:2',
            'images.*'    => 'image|mimes:jpg,jpeg,png|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // Update optional fields
        $product->update(array_filter([
            'name'        => $validated['name'] ?? null,
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'price'       => $validated['price'] ?? null,
            'stock'       => $validated['stock'] ?? null,
            'status'      => $validated['status'] ?? null,
        ]));

        // Jika ada file baru maka hapus gambar lama & upload baru
        if ($request->hasFile('images')) {

            // Hapus gambar lama
            if (is_array($product->images)) {
                foreach ($product->images as $img) {
                    $clean = str_replace('/storage/', '', $img);
                    Storage::disk('public')->delete($clean);
                }
            }

            $newFiles = [];

            foreach ($request->file('images') as $file) {
                $filename = 'product_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $newFiles[] = '/storage/' . $path;
            }

                $product->update(['images' => $newFiles, 'primary_image' => count($newFiles) ? $newFiles[0] : null]);

                // Invalidate public cache
                try { Cache::increment('products_cache_version'); } catch (\Exception $e) { Cache::put('products_cache_version', 1); }
        }

        return response()->json([
            'message' => 'Product updated',
            'product' => $product,
        ]);
    }

    /**
     * Hapus produk
     */
    public function destroy(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        if ($product->seller_id !== $seller->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus gambar
        if (is_array($product->images)) {
            foreach ($product->images as $img) {
                $clean = str_replace('/storage/', '', $img);
                Storage::disk('public')->delete($clean);
            }
        }

            $product->delete();

            // Invalidate public cache
            try { Cache::increment('products_cache_version'); } catch (\Exception $e) { Cache::put('products_cache_version', 1); }

        return response()->json(['message' => 'Product deleted']);
    }

    /**
     * Non-aktifkan produk
     */
    public function deactivate(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        if ($product->seller_id !== $seller->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->update(['is_active' => false]);

        return response()->json(['message' => 'Product deactivated']);
    }

    /**
     * Aktifkan produk
     */
    public function activate(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

        if ($product->seller_id !== $seller->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->update(['is_active' => true]);

        return response()->json(['message' => 'Product activated']);
    }
}


