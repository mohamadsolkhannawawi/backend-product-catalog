<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ProductPublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSellerManagementController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',   [AuthController::class, 'logout']);
        Route::get('/me',        [AuthController::class, 'me']);
    });
});

// Public routes (no auth required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/validate/unique', [ValidationController::class, 'unique']);

// Authenticated users review products
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware('api.auth');

/*
|--------------------------------------------------------------------------
| SELLER ONBOARDING
|--------------------------------------------------------------------------
*/
Route::post('/seller/onboard', [AuthController::class, 'onboard'])
    ->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| SELLER PRODUCTS
|--------------------------------------------------------------------------
*/
// Seller-scoped product management under dashboard/seller
Route::middleware(['auth:sanctum', 'role:seller', 'seller.active'])
    ->prefix('dashboard/seller')
    ->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::post('/products/{product}/activate', [ProductController::class, 'activate']);
        Route::post('/products/{product}/deactivate', [ProductController::class, 'deactivate']);
    });

/*
|--------------------------------------------------------------------------
| SELLER REPORT
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/seller/reports/stock', [ReportController::class, 'sellerReport'])
    ->middleware(['auth:sanctum', 'role:seller', 'seller.active']);

/*
|--------------------------------------------------------------------------
| SELLER DASHBOARD
|--------------------------------------------------------------------------
*/ 
Route::get('/dashboard/seller/overview', [SellerDashboardController::class, 'overview'])
    ->middleware(['auth:sanctum', 'role:seller', 'seller.active']);

// Seller charts & reports (SRS-08, SRS-12..SRS-14)
Route::prefix('dashboard/seller')->middleware(['auth:sanctum', 'role:seller', 'seller.active'])->group(function () {
    Route::get('/charts/stock-per-product', [SellerDashboardController::class, 'stockPerProduct']);
    Route::get('/charts/rating-per-product', [SellerDashboardController::class, 'ratingPerProduct']);
    Route::get('/charts/reviewers-by-province', [SellerDashboardController::class, 'reviewersByProvince']);

    Route::get('/reports/stock', [ReportController::class, 'sellerStockReport']);
    Route::get('/reports/top-rated', [ReportController::class, 'sellerTopRatedReport']);
    Route::get('/reports/restock', [ReportController::class, 'sellerRestockReport']);
});


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/admin/stats', [AdminDashboardController::class, 'index'])
    ->middleware(['auth:sanctum', 'role:admin']);


/*
|--------------------------------------------------------------------------
| ADMIN SELLER MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/sellers/pending', [AdminSellerController::class, 'pending']);
    Route::get('/sellers', [AdminSellerController::class, 'index']);
    Route::get('/sellers/{seller}', [AdminSellerController::class, 'show']);
    Route::post('/sellers/{seller}/approve', [AdminSellerController::class, 'approve']);
    Route::post('/sellers/{seller}/reject', [AdminSellerController::class, 'reject']);
    Route::post('/sellers/{seller}/activate', [AdminSellerManagementController::class, 'activate']);
    Route::post('/sellers/{seller}/deactivate', [AdminSellerManagementController::class, 'deactivate']);
    Route::patch('/sellers/{seller}/toggle-status', [AdminSellerManagementController::class, 'toggleStatus']);
    Route::get('/sellers/{seller}/ktp', [AdminSellerController::class, 'ktpFile']);
    Route::get('/sellers/{seller}/pic', [AdminSellerController::class, 'picFile']);

    // Admin charts for dashboard (SRS-07)
    Route::get('/charts/products-by-category', [AdminDashboardController::class, 'productsByCategory']);
    Route::get('/charts/sellers-by-province', [AdminDashboardController::class, 'sellersByProvince']);
    Route::get('/charts/sellers-status', [AdminDashboardController::class, 'sellersStatus']);
    Route::get('/charts/total-reviewers', [AdminDashboardController::class, 'totalReviewers']);

    // Admin report PDFs (SRS-09..SRS-11)
    Route::get('/reports/sellers', [ReportController::class, 'platformSellersReport']); // ?status=active|inactive
    Route::get('/reports/sellers-by-province', [ReportController::class, 'platformSellersByProvinceReport']);
    Route::get('/reports/top-rated-products', [ReportController::class, 'platformTopRatedProductsReport']);
});


/*
|--------------------------------------------------------------------------
| LOCATIONS
|--------------------------------------------------------------------------
*/
Route::prefix('locations')->group(function () {
    Route::get('/provinces',                [LocationController::class, 'provinces']);
    Route::get('/provinces/{province}/cities',        [LocationController::class, 'cities']);
    Route::get('/cities/{city}/districts',         [LocationController::class, 'districts']);
    Route::get('/districts/{district}/villages',      [LocationController::class, 'villages']);
});

// Categories (public)
Route::get('/categories', [CategoryController::class, 'index']);

// Validation helpers (frontend async checks)
Route::post('/validate/unique', [ValidationController::class, 'unique']);


/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCTS
|--------------------------------------------------------------------------
*/
// Public catalog
Route::get('/catalog', [ProductPublicController::class, 'index']);
Route::get('/catalog/search', [ProductPublicController::class, 'search']);
Route::get('/catalog/filter', [ProductPublicController::class, 'search']);
Route::get('/catalog/{slug}', [ProductPublicController::class, 'show']);

// Reviews (public)
Route::get('/products/{slug}/reviews', [ReviewController::class, 'list']);
Route::post('/reviews', [ReviewController::class, 'store'])->middleware('review.throttle');
// Also accept product_id in the URL for compatibility with tests and public endpoints
Route::post('/catalog/products/{product}/reviews', [ReviewController::class, 'store'])->middleware('review.throttle');

// Public seller verification (signed link)
Route::get('/seller/verify', [AdminSellerController::class, 'verify'])->name('seller.verify');

