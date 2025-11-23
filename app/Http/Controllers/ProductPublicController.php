<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductPublicController extends Controller
{
    /**
     * Public catalog
     */
    public function index(Request $request)
    {
        $products = Product::select(
            'id',
            'name',
            'slug',
            'price',
            'images',
            'created_at'
        )
        ->orderBy('id', 'desc')
        ->paginate(20);

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $product->increment('visitor');

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'id'          => $product->id,
            'name'        => $product->name,
            'slug'        => $product->slug,
            'description' => $product->description,
            'price'       => $product->price,
            'stock'       => $product->stock,
            'images'      => $product->images,
            'seller'      => [
                'id'          => $product->seller->id,
                'store_name'  => $product->seller->store_name,
                'city'        => $product->seller->city?->name,
                'province'    => $product->seller->province?->name,
            ],
            'created_at'  => $product->created_at,
        ]);
    }


    public function search(Request $request)
    {
        $query = Product::query();

        // is_active filter
        if (Schema::hasColumn('products', 'is_active')) {
            $query->where('is_active', true);
        }

        // Keyword search berdasarkan name, description, category (case-insensitive)
        if ($request->filled('q')) {
            $q = mb_strtolower($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(category) LIKE ?', ["%{$q}%"]);
            });
        }

        // Price filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Mengembalikan fields katalog publik yang sama dan paginasi seperti `index`
        $products = $query->select(
            'id',
            'name',
            'slug',
            'price',
            'images',
            'created_at'
        )
        ->paginate(20);

        return response()->json($products);
    }

}
