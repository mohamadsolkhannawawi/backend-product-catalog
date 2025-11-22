<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function sellerReport(Request $request)
    {
        $seller = Seller::where('user_id', $request->user()->id)
            ->with([
                'products' => function ($q) {
                    $q->select('id', 'seller_id', 'name', 'price', 'stock');
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
}
