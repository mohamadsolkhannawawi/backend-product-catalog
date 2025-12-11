<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerPdfReportController;

Route::middleware(['auth:sanctum', 'seller'])->group(function () {
    // Seller PDF Reports
    Route::prefix('seller/reports')->group(function () {
        // Download reports
        Route::post('/dashboard/download', [SellerPdfReportController::class, 'dashboardReport'])
            ->name('seller.report.dashboard.download');
        Route::post('/stock/download', [SellerPdfReportController::class, 'stockReport'])
            ->name('seller.report.stock.download');
        Route::post('/top-rated/download', [SellerPdfReportController::class, 'topRatedReport'])
            ->name('seller.report.top-rated.download');
        Route::post('/restock/download', [SellerPdfReportController::class, 'restockReport'])
            ->name('seller.report.restock.download');
        Route::post('/sales/download', [SellerPdfReportController::class, 'salesReport'])
            ->name('seller.report.sales.download');
        Route::post('/reviews/download', [SellerPdfReportController::class, 'reviewsReport'])
            ->name('seller.report.reviews.download');

        // View reports in browser
        Route::get('/view', [SellerPdfReportController::class, 'viewReport'])
            ->name('seller.report.view');
    });
});
