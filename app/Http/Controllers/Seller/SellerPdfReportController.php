<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SellerPdfReportController extends Controller
{
    /**
     * (SRS-MartPlace-12) Seller Products Report by Stock
     * Laporan Daftar Produk Berdasarkan Stok
     */
    public function stockReport(Request $request)
    {
        try {
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = Product::where('seller_id', $seller->seller_id)
                ->with('category')
                ->select(
                    'products.product_id',
                    'products.name',
                    'products.category_id',
                    'products.price',
                    'products.stock',
                    DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
                )
                ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
                ->groupBy('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock')
                ->orderByDesc('stock')
                ->get();

            $pdf = Pdf::loadView('pdf.seller-products-by-stock-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN STOK',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->download('laporan-produk-stok-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('Seller stock report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-13) Seller Products Report by Rating
     * Laporan Daftar Produk Berdasarkan Rating
     */
    public function topRatedReport(Request $request)
    {
        try {
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = Product::where('seller_id', $seller->seller_id)
                ->with('category')
                ->select(
                    'products.product_id',
                    'products.name',
                    'products.category_id',
                    'products.price',
                    'products.stock',
                    DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
                )
                ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
                ->groupBy('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock')
                ->orderByDesc('avg_rating')
                ->get();

            $pdf = Pdf::loadView('pdf.seller-products-by-rating-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN RATING',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->download('laporan-produk-rating-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('Seller top rated report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-14) Seller Products Restock Report
     * Laporan Daftar Produk Segera Dipesan
     */
    public function restockReport(Request $request)
    {
        try {
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = Product::where('seller_id', $seller->seller_id)
                ->where('stock', '<', 2)
                ->with('category')
                ->select(
                    'products.product_id',
                    'products.name',
                    'products.category_id',
                    'products.price',
                    'products.stock'
                )
                ->orderBy('category_id', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            $pdf = Pdf::loadView('pdf.seller-products-restock-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK SEGERA DIPESAN',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->download('laporan-produk-restock-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('Seller restock report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View report in browser (stream)
     */
    public function viewReport(Request $request)
    {
        try {
            $reportType = $request->query('type', 'stock');
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = [];
            $view = '';

            switch ($reportType) {
                case 'top-rated':
                    $data = Product::where('seller_id', $seller->seller_id)
                        ->with('category')
                        ->select('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock',
                            DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating'))
                        ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
                        ->groupBy('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock')
                        ->orderByDesc('avg_rating')
                        ->get();
                    $view = 'pdf.seller-products-by-rating-formal';
                    break;
                case 'restock':
                    $data = Product::where('seller_id', $seller->seller_id)
                        ->where('stock', '<', 2)
                        ->with('category')
                        ->select('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock')
                        ->orderBy('category_id', 'asc')
                        ->orderBy('name', 'asc')
                        ->get();
                    $view = 'pdf.seller-products-restock-formal';
                    break;
                default:
                    $data = Product::where('seller_id', $seller->seller_id)
                        ->with('category')
                        ->select('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock',
                            DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating'))
                        ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
                        ->groupBy('products.product_id', 'products.name', 'products.category_id', 'products.price', 'products.stock')
                        ->orderByDesc('stock')
                        ->get();
                    $view = 'pdf.seller-products-by-stock-formal';
                    break;
            }

            $pdf = Pdf::loadView($view, [
                'data' => $data,
                'reportTitle' => 'LAPORAN PENJUAL',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->stream('laporan-penjual-' . $reportType . '.pdf');
        } catch (\Exception $e) {
            logger()->error('View seller report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menampilkan laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Seller Sales Report (PDF)
     */
    public function salesReport(Request $request)
    {
        try {
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = Product::where('seller_id', $seller->seller_id)
                ->with('category')
                ->select(
                    'products.product_id',
                    'products.name',
                    'products.category_id',
                    'products.price',
                    'products.stock'
                )
                ->orderBy('products.created_at', 'desc')
                ->limit(50)
                ->get();

            $pdf = Pdf::loadView('pdf.seller-sales-report', [
                'data' => $data,
                'reportTitle' => 'LAPORAN PENJUALAN',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->download('laporan-penjualan-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('Seller sales report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Seller Reviews Report (PDF)
     */
    public function reviewsReport(Request $request)
    {
        try {
            $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();

            $data = Review::whereHas('product', function ($q) use ($seller) {
                    $q->where('seller_id', $seller->seller_id);
                })
                ->with('product:product_id,name', 'reviewer:user_id,name')
                ->select(
                    'review_id',
                    'product_id',
                    'user_id',
                    'rating',
                    'comment',
                    'created_at'
                )
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $pdf = Pdf::loadView('pdf.seller-reviews-report', [
                'data' => $data,
                'reportTitle' => 'LAPORAN ULASAN DAN RATING',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            return $pdf->download('laporan-ulasan-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('Seller reviews report failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }
}
