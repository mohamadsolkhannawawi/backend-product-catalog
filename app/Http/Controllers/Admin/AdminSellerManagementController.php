<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class AdminSellerManagementController extends Controller
{
    // List all sellers
    public function index()
    {
        return Seller::with('user:id,name,email')
            ->select('id', 'user_id', 'store_name', 'status', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Activate seller account
    public function activate(Seller $seller)
    {
        $seller->update([
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Seller activated successfully',
            'seller' => $seller,
        ]);
    }

    // Deactivate seller account
    public function deactivate(Seller $seller)
    {
        $seller->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Seller deactivated successfully',
            'seller' => $seller,
        ]);
    }

    // Toggle seller active/inactive status
    public function toggleStatus(Seller $seller)
    {
        $seller->update([
            'is_active' => !$seller->is_active
        ]);

        return response()->json([
            'message' => 'Seller status toggled successfully',
            'seller' => $seller,
        ]);
    }
}
