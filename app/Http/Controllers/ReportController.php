<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Helper method to set page numbering for PDF
     * Uses Dompdf's built-in page_script callback
     */
    private function addPageNumbering($pdf)
    {
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        
        $canvas->page_script(function($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Halaman {$pageNumber} dari {$pageCount}";
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->getTextWidth($text, $font, 9);
            
            // Position: bottom right (near page edge)
            $x = $canvas->get_width() - $width - 15;
            $y = $canvas->get_height() - 15;
            
            $canvas->text($x, $y, $text, $font, 9, [0.33, 0.33, 0.33]);
        });
        
        return $pdf;
    }

    // ============================================================================
    // ADMIN REPORTS - Now using formal PDF template
    // ============================================================================

    /**
     * (SRS-MartPlace-09) Admin Sellers Report by Status (PDF)
     * Status = Aktif (is_active=true) atau Tidak Aktif (is_active=false)
     */
    public function platformSellersReport(Request $request)
    {
        try {
            $data = Seller::with('user:user_id,name')
                ->select('seller_id', 'user_id', 'store_name', 'is_active', 'phone', 'pic_name')
                ->where('status', 'approved') // Only approved sellers
                ->orderByRaw("CASE WHEN is_active = true THEN 0 ELSE 1 END") // Aktif dulu baru Tidak Aktif
                ->orderBy('store_name')
                ->get();

            $pdf = Pdf::loadView('pdf.admin-sellers-by-status', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR AKUN PENJUAL BERDASARKAN STATUS',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-penjual-status-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('platformSellersReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-10) Admin Sellers by Province Report (PDF)
     */
    public function platformSellersByProvinceReport(Request $request)
    {
        try {
            $data = Seller::with(['province:code,name'])
                ->select('seller_id', 'user_id', 'store_name', 'province_id', 'pic_name', 'phone')
                ->where('status', 'approved')
                ->where('is_active', true)
                ->orderBy('province_id')
                ->orderBy('store_name')
                ->get();

            $pdf = Pdf::loadView('pdf.admin-sellers-by-province-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR TOKO BERDASARKAN LOKASI PROVINSI',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-toko-provinsi-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('platformSellersByProvinceReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-11) Admin Products by Rating Report (PDF)
     * Per-province breakdown: one row per (product, province) combination
     * Each row shows: Product, Category, Price, Rating (avg for that product in that province), Store Name, Province
     * Sorted by rating descending
     */
    public function platformTopRatedProductsReport(Request $request)
    {
        try {
            // Get all reviews with product and seller info
            // Group by (product_id, province_id) and calculate average rating per group
            $reviews = Review::with([
                'product:product_id,name,category_id,price,seller_id,is_active',
                'product.category:category_id,name',
                'product.seller:seller_id,store_name,province_id',
                'province:code,name'
            ])
                ->where('reviews.rating', '>', 0)
                ->get();

            // Group by (product_id, province_id) and calculate average rating
            $groupedData = $reviews->groupBy(function ($review) {
                return $review->product_id . '-' . $review->province_id;
            })->map(function ($group) {
                $firstReview = $group->first();
                $avgRating = $group->avg('rating');
                
                return [
                    'product_id' => $firstReview->product_id,
                    'product_name' => $firstReview->product->name,
                    'category_name' => $firstReview->product->category->name ?? '-',
                    'price' => $firstReview->product->price,
                    'store_name' => $firstReview->product->seller->store_name ?? '-',
                    'province_name' => $firstReview->province?->name ?? 'Unknown',
                    'avg_rating' => round($avgRating, 2),
                    'review_count' => $group->count(),
                ];
            });

            // Sort by rating descending, then by product name
            $data = $groupedData
                ->sortByDesc('avg_rating')
                ->sortBy('product_name')
                ->values();

            $pdf = Pdf::loadView('pdf.admin-products-by-rating-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN RATING',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-produk-rating-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('platformTopRatedProductsReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================================
    // SELLER REPORTS - Now using formal PDF template
    // ============================================================================

    /**
     * (SRS-MartPlace-12) Seller Report (legacy - redirects to new controller)
     */
    public function sellerReport(Request $request)
    {
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

        try {
            $pdf = Pdf::loadView('pdf.seller-products-by-stock-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN STOK',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-produk-stok-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('sellerReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-12) Seller Stock Report
     */
    public function sellerStockReport(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();
        $data = Product::where('seller_id', $seller->seller_id)
            ->with('category')
            ->select(
                'products.product_id',
                'products.name',
                'products.category_id',
                'products.price',
                'products.stock',
                DB::raw('COALESCE(AVG(reviews.rating),0) as avg_rating')
            )
            ->leftJoin('reviews','reviews.product_id','=','products.product_id')
            ->groupBy('products.product_id','products.name','products.category_id','products.price','products.stock')
            ->orderByDesc('stock')
            ->get();

        try {
            $pdf = Pdf::loadView('pdf.seller-products-by-stock-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN STOK',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-produk-stok-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('sellerStockReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-13) Seller Top Rated Report
     */
    public function sellerTopRatedReport(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)->firstOrFail();
        $data = Product::where('seller_id', $seller->seller_id)
            ->with('category')
            ->select(
                'products.product_id',
                'products.name',
                'products.category_id',
                'products.price',
                'products.stock',
                DB::raw('COALESCE(AVG(reviews.rating),0) as avg_rating')
            )
            ->leftJoin('reviews','reviews.product_id','=','products.product_id')
            ->groupBy('products.product_id','products.name','products.category_id','products.price','products.stock')
            ->orderByDesc('avg_rating')
            ->get();

        try {
            $pdf = Pdf::loadView('pdf.seller-products-by-rating-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN RATING',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-produk-rating-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('sellerTopRatedReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * (SRS-MartPlace-14) Seller Restock Report
     */
    public function sellerRestockReport(Request $request)
    {
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

        try {
            $pdf = Pdf::loadView('pdf.seller-products-restock-formal', [
                'data' => $data,
                'reportTitle' => 'LAPORAN DAFTAR PRODUK SEGERA DIPESAN',
                'reportDate' => now()->format('d-m-Y'),
            ])->setPaper('a4');

            $this->addPageNumbering($pdf);

            return $pdf->download('laporan-produk-restock-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            logger()->error('sellerRestockReport failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Seller Sales Report (for JSON endpoint)
     */
    public function sellerSalesReport(Request $request)
    {
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

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Sales report data',
        ]);
    }

    /**
     * Seller Reviews Report (for JSON endpoint)
     */
    public function sellerReviewsReport(Request $request)
    {
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

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Reviews report data',
        ]);
    }
}
