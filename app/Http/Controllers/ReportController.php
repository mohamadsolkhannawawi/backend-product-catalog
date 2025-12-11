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
     */
    public function platformSellersReport(Request $request)
    {
        try {
            $data = Seller::with('user:user_id,name')
                ->select('seller_id', 'user_id', 'store_name', 'status', 'phone', 'pic_name')
                ->where('is_active', true)
                ->orderByRaw("CASE WHEN status = 'approved' THEN 0 ELSE 1 END")
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
     */
    public function platformTopRatedProductsReport(Request $request)
    {
        try {
            // Get products with reviews, calculating average rating
            $data = Product::with([
                'category:category_id,name',
                'seller:seller_id,store_name',
                'reviews' => function ($query) {
                    $query->with('province:code,name')
                        ->orderByDesc('created_at');
                }
            ])
                ->where('products.is_active', true)
                ->get()
                ->filter(function ($product) {
                    // Only include products with reviews
                    return $product->reviews->count() > 0;
                })
                ->map(function ($product) {
                    // Calculate average rating and add it
                    $product->avg_rating = $product->reviews->avg('rating');
                    // Get latest review with province
                    $product->latest_review = $product->reviews->first();
                    return $product;
                })
                ->sortByDesc('avg_rating')
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
