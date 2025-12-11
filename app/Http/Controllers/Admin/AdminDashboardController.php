<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function stats()
    {
        // Total seller
        $totalSellers = Seller::count();
        $totalPending = Seller::where('status', 'pending')->count();
        $totalApproved = Seller::where('status', 'approved')->count();

        // Produk & Review statistik
        $totalProducts = Product::count();
        $totalReviews = Review::count();

        // Rating rata-rata global
        $averageRating = Review::avg('rating');

        // Persentase review positif (rating ≥ 4)
        $positiveReviews = Review::where('rating', '>=', 4)->count();
        $positivePercentage = $totalReviews > 0
            ? round(($positiveReviews / $totalReviews) * 100, 2)
            : 0;

        // Produk terbaru
        $latestProducts = Product::latest()->take(10)->get(['id','name','slug','created_at']);

        // Seller terbaru
        $latestSellers = Seller::latest()->take(10)->get(['id','store_name','status','created_at']);

        // Dummy visitor count (MVP)
        $totalVisitors = Product::sum('visitor');

        return response()->json([
            'sellers' => [
                'total' => $totalSellers,
                'pending' => $totalPending,
                'approved' => $totalApproved,
            ],
            'products' => [
                'total' => $totalProducts,
                'latest' => $latestProducts,
            ],
            'reviews' => [
                'total' => $totalReviews,
                'average_rating' => round($averageRating,2),
                'positive_percentage' => $positivePercentage,
            ],
            'latest_sellers' => $latestSellers,
            'visitors' => $totalVisitors,
        ]);
    }

    // Route expects 'index' — provide a thin wrapper for compatibility
    public function index(Request $request)
    {
        return $this->stats();
    }

    /**
     * Chart data: products grouped by category (SRS-07)
     */
    public function productsByCategory()
    {
        $data = Product::select('category_id')
            ->with('category:category_id,name')
            ->whereNotNull('category_id')
            ->where('is_active', true)
            ->get()
            ->groupBy('category_id')
            ->map(function ($group) {
                $category = $group->first()->category;
                return [
                    'category_id' => $group->first()->category_id,
                    'category_name' => $category ? $category->name : 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->values();

        return response()->json(['data' => $data]);
    }

    /**
     * Chart data: sellers grouped by province (SRS-07)
     */
    public function sellersByProvince()
    {
        $data = Seller::select('province_id')
            ->selectRaw('count(*) as count')
            ->where('status', 'approved')
            ->where('province_id', '!=', null)
            ->groupBy('province_id')
            ->orderByRaw('count DESC')
            ->with('province:code,name')
            ->get()
            ->map(function ($item) {
                return [
                    'province_id' => $item->province_id,
                    'province_name' => $item->province?->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        return response()->json(['data' => $data]);
    }

    /**
     * Chart data: seller status distribution (SRS-07)
     */
    public function sellersStatus()
    {
        $data = [
            ['status' => 'active', 'count' => Seller::where('is_active', true)->count()],
            ['status' => 'inactive', 'count' => Seller::where('is_active', false)->count()],
            ['status' => 'pending', 'count' => Seller::where('status', 'pending')->count()],
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Chart data: total unique reviewers (SRS-07)
     */
    public function totalReviewers()
    {
        // Count distinct reviewer emails
        $count = Review::distinct('email')->count('email');
        return response()->json(['data' => ['count' => $count]]);
    }
}
