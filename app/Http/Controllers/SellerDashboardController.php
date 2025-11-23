<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use App\Models\Review;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    public function overview(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        // 1) Jumlah produk
        $productCount = Product::where('seller_id', $seller->id)->count();

        // 2) Jumlah review untuk semua produk seller
        $reviewCount = Review::whereIn(
            'product_id',
            Product::where('seller_id', $seller->id)->pluck('id')
        )->count();

        // 3) Rata-rata rating
        $averageRating = Review::whereIn(
            'product_id',
            Product::where('seller_id', $seller->id)->pluck('id')
        )->avg('rating');

        $averageRating = round($averageRating ?? 0, 2);

        // 4) Persentase review positif (rating >= 4)
        $positiveReviews = Review::whereIn(
            'product_id',
            Product::where('seller_id', $seller->id)->pluck('id')
        )->where('rating', '>=', 4)->count();

        $positivePercentage = $reviewCount > 0
            ? round(($positiveReviews / $reviewCount) * 100, 2)
            : 0;

        // 5) Produk terbaru
        $latestProducts = Product::where('seller_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'slug', 'created_at']);

        // 6) Produk terpopuler berdasarkan views (dummy MVP)
        $totalViews = Product::where('seller_id', $seller->id)->sum('views');
        $topViewed = Product::where('seller_id', $seller->id)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'product_count'       => $productCount,
            'review_count'        => $reviewCount,
            'average_rating'      => $averageRating,
            'positive_percentage' => $positivePercentage,
            'total_views'         => $totalViews,
            'latest_products'     => $latestProducts,
        ]);
    }
}
