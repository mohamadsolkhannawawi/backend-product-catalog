<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            ->select('id', 'name', 'slug', 'price', 'stock', 'images', 'created_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($products);
    }

    /**
     * Create produk
     */
    public function store(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',

            'images'      => 'nullable|array',
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
            'seller_id'   => $seller->id,
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'stock'       => $validated['stock'],
            'category'    => $validated['category'] ?? null,
            'images'      => $imagePaths,
        ]);

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
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        if ($product->seller_id !== $seller->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($product);
    }

    /**
     * Update produk
     * POST form-data untuk file upload
     */
    public function update(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        if ($product->seller_id !== $seller->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'price'       => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',

            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // Update optional fields
        $product->update(array_filter([
            'name'        => $validated['name'] ?? null,
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'] ?? null,
            'price'       => $validated['price'] ?? null,
            'stock'       => $validated['stock'] ?? null,
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

            $product->update(['images' => $newFiles]);
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
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        if ($product->seller_id !== $seller->id) {
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

        return response()->json(['message' => 'Product deleted']);
    }

    /**
     * Non-aktifkan produk
     */
    public function deactivate(Request $request, Product $product)
    {
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        if ($product->seller_id !== $seller->id) {
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
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        if ($product->seller_id !== $seller->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->update(['is_active' => true]);

        return response()->json(['message' => 'Product activated']);
    }
}


