<?php

/**
 * PDF Report Routes for Admin
 * 
 * Add these routes to routes/api.php or routes/admin.php
 * 
 * Usage:
 * POST /api/admin/reports/sellers/download
 * GET /api/admin/reports/sellers/view
 */

use App\Http\Controllers\Admin\PdfReportController;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/reports')->group(function () {
    
    // Seller Reports
    Route::post('sellers/download', [PdfReportController::class, 'downloadSellersByProvinceReport'])
        ->name('reports.sellers.download');
    
    Route::get('sellers/view', [PdfReportController::class, 'viewSellerReport'])
        ->name('reports.sellers.view');

    // Product Reports
    Route::post('products/download', [PdfReportController::class, 'downloadProductsReport'])
        ->name('reports.products.download');

    // Save to Storage
    Route::post('save', [PdfReportController::class, 'savePdfToStorage'])
        ->name('reports.save');
});
