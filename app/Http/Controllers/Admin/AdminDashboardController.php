<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
