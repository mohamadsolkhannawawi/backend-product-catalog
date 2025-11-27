<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sellerReport(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->user_id)
            ->with([
                'products' => function ($q) {
                    $q->select('product_id', 'seller_id', 'name', 'price', 'stock');
                }
            ])
            ->firstOrFail();

        $html = view('pdf.seller-report', [
            'seller'   => $seller,
            'products' => $seller->products,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="seller-report.pdf"');
    }

    // ADMIN: platform sellers report (PDF or JSON)
    public function platformSellersReport(Request $request)
    {
        $status = $request->query('status');

        $query = Seller::with('user:user_id,name,email')
            ->select('seller_id','user_id','store_name','status','is_active','province_id','created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $sellers = $query->get();

        // If PDF requested, render PDF
        if ($request->query('format') === 'pdf') {
            try {
                $html = view('pdf.admin-sellers', ['sellers' => $sellers])->render();
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->render();

                return response($dompdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="platform-sellers.pdf"');
            } catch (\Exception $e) {
                // Log and return JSON error for easier debugging from frontend
                logger()->error('platformSellersReport PDF generation failed: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['data' => $sellers]);
    }

    public function platformSellersByProvinceReport(Request $request)
    {
        $data = Seller::with('user:user_id,name,email')
            ->select('seller_id','user_id','store_name','province_id','phone','is_active','created_at')
            ->where('status', 'approved')
            ->orderBy('province_id')
            ->get();

        if ($request->query('format') === 'pdf') {
            try {
                $html = view('pdf.admin-sellers-by-province', ['data' => $data])->render();
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->render();

                return response($dompdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="sellers-by-province.pdf"');
            } catch (\Exception $e) {
                logger()->error('platformSellersByProvinceReport PDF generation failed: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['data' => $data]);
    }

    public function platformTopRatedProductsReport(Request $request)
    {
        $data = Product::with(['seller:seller_id,store_name,province_id', 'category:category_id,name'])
            ->select('products.product_id','products.name','products.category_id','products.price','products.seller_id', DB::raw('COALESCE(AVG(reviews.rating),0) as avg_rating'))
            ->leftJoin('reviews', 'reviews.product_id', '=', 'products.product_id')
            ->where('products.is_active', true)
            ->groupBy('products.product_id','products.name','products.category_id','products.price','products.seller_id')
            ->orderByDesc('avg_rating')
            ->get();

        // Add reviewer province info to each product (province where highest/latest review came from)
        $data->each(function ($product) {
            $reviewerProvince = Review::where('product_id', $product->product_id)
                ->orderByDesc('created_at')
                ->value('province_id') ?? 'N/A';
            $product->reviewer_province = $reviewerProvince;
        });

        if ($request->query('format') === 'pdf') {
            try {
                $html = view('pdf.admin-top-rated-products', ['data' => $data])->render();
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->render();

                return response($dompdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="top-rated-products.pdf"');
            } catch (\Exception $e) {
                logger()->error('platformTopRatedProductsReport PDF generation failed: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['data' => $data]);
    }

    // Seller specific reports (JSON for MVP)
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

        if ($request->query('format') === 'pdf') {
            $html = view('pdf.seller-stock-report', ['seller' => $seller, 'data' => $data])->render();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="seller-stock-report.pdf"');
        }

        return response()->json($data);
    }

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

        if ($request->query('format') === 'pdf') {
            $html = view('pdf.seller-top-rated-report', ['seller' => $seller, 'data' => $data])->render();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="seller-top-rated-report.pdf"');
        }

        return response()->json($data);
    }

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

        if ($request->query('format') === 'pdf') {
            $html = view('pdf.seller-restock-report', ['seller' => $seller, 'data' => $data])->render();
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="seller-restock-report.pdf"');
        }

        return response()->json($data);
    }
}
