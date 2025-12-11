<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class AdminSellerManagementController extends Controller
{
    // List all sellers (only approved and verified can be toggled)
    public function index()
    {
        return Seller::with('user:user_id,name,email')
            ->select('seller_id', 'user_id', 'store_name', 'status', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Activate seller account (only if status='approved' AND is_active=true)
    public function activate(Seller $seller)
    {
        // Only allow activation if seller is already approved AND verified
        if ($seller->status !== 'approved' || !$seller->is_active) {
            return response()->json([
                'message' => 'Hanya seller yang sudah di-approve dan terverifikasi yang bisa diaktifkan',
                'current_status' => $seller->status,
                'is_active' => $seller->is_active,
            ], 422);
        }

        // This is redundant since is_active=true already, but keeping for clarity
        $seller->update([
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Penjual berhasil diaktifkan',
            'seller' => $seller,
        ]);
    }

    // Deactivate seller account (only if already verified/approved)
    public function deactivate(Seller $seller)
    {
        // Only allow deactivation if seller is already approved AND verified
        if ($seller->status !== 'approved') {
            return response()->json([
                'message' => 'Hanya seller yang sudah di-approve yang bisa dinonaktifkan',
                'current_status' => $seller->status,
            ], 422);
        }

        $seller->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Penjual berhasil dinonaktifkan',
            'seller' => $seller,
        ]);
    }

    // Toggle seller active/inactive status (only if already approved)
    public function toggleStatus(Seller $seller)
    {
        // Only allow toggle if seller is already approved
        if ($seller->status !== 'approved') {
            return response()->json([
                'message' => 'Hanya seller yang sudah di-approve yang bisa di-toggle statusnya',
                'current_status' => $seller->status,
            ], 422);
        }

        $seller->update([
            'is_active' => !$seller->is_active
        ]);

        return response()->json([
            'message' => 'Status penjual berhasil diubah',
            'seller' => $seller,
        ]);
    }
}
