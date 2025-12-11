<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductPublicController extends Controller
{
    /**
     * Public catalog
     */
    public function index(Request $request)
    {
        $version = Cache::get('products_cache_version', 1);

        // Use full URL hash so different filters produce different cache entries
        $paramsHash = md5(strtolower($request->fullUrl()));
        $cacheKey = "products:index:v{$version}:{$paramsHash}:page:{$request->get('page',1)}";

        $idColumn = Schema::hasColumn('products', 'product_id') ? 'product_id' : 'id';
        $selectId = $idColumn === 'product_id' ? 'product_id' : DB::raw('id as product_id');

        $products = Cache::remember($cacheKey, 60, function () use ($request, $idColumn, $selectId) {
            $query = Product::with('seller.city', 'seller.province', 'category')
                ->select(
                    $selectId,
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'category',
                    'category_id',
                    'images',
                    'primary_image',
                    'seller_id',
                    'created_at'
                )
                ->where('is_active', true);

            // Allow filtering by category slug for icon-based filtering from frontend
            if ($request->filled('category_slug')) {
                $cat = \App\Models\Category::where('slug', $request->category_slug)->first();
                if ($cat) {
                    $query->where('category_id', $cat->category_id);
                }
            } elseif ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            return $query->orderBy($idColumn, 'desc')->paginate(20);
        });

        // Apply rating aggregation after cache (so it always recalculates)
        $products->getCollection()->each(function ($p) {
            // Manually calculate average rating for each product
            $avgRating = DB::table('reviews')
                ->where('product_id', $p->product_id)
                ->avg('rating');
            $p->average_rating = $avgRating ? round((float) $avgRating, 2) : 0;
            $p->city = $p->seller?->city?->name;
            $p->province = $p->seller?->province?->name;
            
            return $p;
        });

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['seller.city', 'seller.province', 'reviews', 'category'])
            ->firstOrFail();

        // Calculate average rating fresh (not cached)
        $avgRating = DB::table('reviews')
            ->where('product_id', $product->product_id)
            ->avg('rating');

        // only increment if the visitor column exists (defensive for mixed migration states)
        if (Schema::hasColumn('products', 'visitor')) {
            try {
                $product->increment('visitor');
            } catch (\Exception $e) {
                // swallow increment errors to avoid 500 on read
                logger()->warning('Could not increment product visitor', ['id' => $product->getKey(), 'error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'product_id'      => $product->product_id,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'description'     => $product->description,
            'category'        => $product->category,
            'price'           => $product->price,
            'stock'           => $product->stock,
            'primary_image'   => $product->primary_image,
            'images'          => $product->images,
            'average_rating'  => $avgRating ? round((float) $avgRating, 2) : 0,
            'seller'          => [
                'seller_id'   => $product->seller->seller_id,
                'store_name'  => $product->seller->store_name,
                'store_description' => $product->seller->store_description,
                'phone'       => $product->seller->phone,
                'city'        => $product->seller->city?->name,
                'province'    => $product->seller->province?->name,
            ],
            'created_at'      => $product->created_at,
        ]);
    }


    public function search(Request $request)
    {
        $query = Product::query();

        // is_active filter
        if (Schema::hasColumn('products', 'is_active')) {
            $query->where('is_active', true);
        }

        // Allow filtering by category slug (frontend icon click) or by id
        if ($request->filled('category_slug')) {
            $cat = \App\Models\Category::where('slug', $request->category_slug)->first();
            if ($cat) {
                $query->where('category_id', $cat->category_id);
            }
        } elseif ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by seller store_name (case-insensitive)
        if ($request->filled('store_name')) {
            $q = mb_strtolower($request->store_name);
            $query->whereHas('seller', function ($sq) use ($q) {
                $sq->whereRaw('LOWER(store_name) LIKE ?', ["%{$q}%"]);
            });
        }

        // Filter by seller province_id and city_id
        if ($request->filled('province_id')) {
            $query->whereHas('seller', function ($sq) use ($request) {
                $sq->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('seller', function ($sq) use ($request) {
                $sq->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('district_id')) {
            $query->whereHas('seller', function ($sq) use ($request) {
                $sq->where('district_id', $request->district_id);
            });
        }

        if ($request->filled('village_id')) {
            $query->whereHas('seller', function ($sq) use ($request) {
                $sq->where('village_id', $request->village_id);
            });
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

        // Mengembalikan fields katalog publik yang sama dan paginasi seperti `index`
        $version = Cache::get('products_cache_version', 1);
        $paramsHash = md5(strtolower($request->fullUrl()));
        $cacheKey = "products:search:v{$version}:{$paramsHash}:page:{$request->get('page',1)}";

        $products = Cache::remember($cacheKey, 60, function () use ($query, $request) {
            // Add joins and aggregates BEFORE select and sorting
            if ($request->get('sort') === 'rating_desc') {
                // For rating sort, use leftJoin with GROUP BY to explicitly calculate avg_rating
                $query->leftJoin('reviews', 'products.product_id', '=', 'reviews.product_id')
                    ->select(
                        'products.product_id',
                        'products.name',
                        'products.slug',
                        'products.price',
                        'products.stock',
                        'products.category',
                        'products.category_id',
                        'products.images',
                        'products.primary_image',
                        'products.seller_id',
                        'products.created_at',
                        DB::raw('COALESCE(AVG(reviews.rating), 0) as reviews_avg_rating')
                    )
                    ->groupBy(
                        'products.product_id'
                    )
                    ->orderByDesc('reviews_avg_rating');
            } else {
                // For other sorts, just select normally
                $query->select(
                    'products.product_id',
                    'products.name',
                    'products.slug',
                    'products.price',
                    'products.stock',
                    'products.category',
                    'products.category_id',
                    'products.images',
                    'products.primary_image',
                    'products.seller_id',
                    'products.created_at'
                );

                // Apply sorting for non-rating sorts
                switch ($request->get('sort')) {
                    case 'price_asc':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price_desc':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'newest':
                    default:
                        $query->orderBy('products.product_id', 'desc');
                        break;
                }
            }

            return $query->with('seller.city', 'seller.province', 'category')
                ->paginate(20);
        });

        // Normalize average rating into `average_rating` float and add seller location
        $products->getCollection()->transform(function ($p) {
            // Calculate average rating fresh (after cache)
            $avgRating = DB::table('reviews')
                ->where('product_id', $p->product_id)
                ->avg('rating');
            $p->average_rating = $avgRating ? round((float) $avgRating, 2) : 0;
            $p->city = $p->seller?->city?->name;
            $p->province = $p->seller?->province?->name;
            
            return $p;
        });

        return response()->json($products);
    }

}
