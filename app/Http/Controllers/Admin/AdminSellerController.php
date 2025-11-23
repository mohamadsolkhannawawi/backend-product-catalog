<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SellerApproved;
use App\Notifications\SellerRejected;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AdminSellerController extends Controller
{
    // List all sellers
    public function index()
    {
        $sellers = Seller::with('user')->get();

        return response()->json($sellers);
    }

    // List pending sellers
    public function pending()
    {
        $pending = Seller::where('status', 'pending')->get();

        return response()->json($pending);
    }

    // Show single seller
    public function show(Seller $seller)
    {
        $seller->load('user');
        return response()->json($seller);
    }

    // Approve seller
    public function approve(Request $request, Seller $seller)
    {
        if ($seller->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 409);
        }

        $seller->update([
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        // Kirim email ke user (gunakan snapshot primitif sehingga aman untuk queued jobs)
        $seller->load('user');
        try {
            if ($seller->user) {
                $sellerSnapshot = [
                    'seller_id' => $seller->id,
                    'company_name' => $seller->company_name ?? null,
                    'user_id' => $seller->user ? $seller->user->id : null,
                ];

                $seller->user->notify(new SellerApproved($sellerSnapshot));
            } else {
                Log::warning('Seller approved but related user not found', ['seller_id' => $seller->id]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send SellerApproved notification', ['seller_id' => $seller->id, 'error' => $e->getMessage()]);
            // Return success untuk tindakan approve tetapi indikasi kegagalan notifikasi
            return response()->json(['message' => 'Seller approved, but notification failed'], 200);
        }

        return response()->json(['message' => 'Seller approved', 'seller' => $seller]);
    }

    // Reject seller
    public function reject(Request $request, Seller $seller)
    {
        Log::info('AdminSellerController@reject called', ['seller_id' => $seller->id, 'seller_status' => $seller->status]);
        $request->validate([
            'reason' => 'required|string',
        ]);

        if ($seller->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 409);
        }

        // Load user relation and store file paths before deleting
        // Also capture rejection reason and a small snapshot of seller data
        $seller->load('user');
        $user = $seller->user;
        $ktpFilePath = $seller->ktp_file_path;
        $picFilePath = $seller->pic_file_path;

        // Capture reason and a primitive snapshot (safe to serialize for queued jobs)
        $reason = $request->input('reason');
        $sellerSnapshot = [
            'company_name' => $seller->company_name ?? null,
            'registration_number' => $seller->registration_number ?? null,
            'ktp_file_path' => $ktpFilePath ?? null,
            'pic_file_path' => $picFilePath ?? null,
        ];

        Log::info('About to run reject transaction', ['seller_id' => $seller->id, 'user_id' => $user ? $user->id : null]);

        // Use transaction to revert user role and delete seller safely
        DB::transaction(function () use ($seller, $user, $request) {
            if ($user) {
                $user->role = 'customer';
                $user->save();
                Log::info('User role reverted to customer', ['user_id' => $user->id]);
            }

            // Force delete seller record (permanent deletion)
            $seller->forceDelete();
            Log::info('Seller force deleted inside transaction', ['seller_id' => $seller->id]);
        });

        // Delete uploaded files from disk (outside transaction)
        if ($ktpFilePath && Storage::disk('public')->exists($ktpFilePath)) {
            Storage::disk('public')->delete($ktpFilePath);
            Log::info('Deleted KTP file', ['path' => $ktpFilePath]);
        }
        if ($picFilePath && Storage::disk('public')->exists($picFilePath)) {
            Storage::disk('public')->delete($picFilePath);
            Log::info('Deleted PIC file', ['path' => $picFilePath]);
        }

        // Notify user after transaction (outside transaction)
        try {
            if ($user) {
                // Send primitives (reason + snapshot) so queued jobs still have data
                $user->notify(new SellerRejected($reason, $sellerSnapshot));
            } else {
                Log::warning('Seller rejected but related user not found (post-delete)', ['seller_id' => $seller->id]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send SellerRejected notification', ['seller_id' => $seller->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Seller rejected and deleted, but notification failed'], 200);
        }

        return response()->json(['message' => 'Seller rejected and deleted', 'user' => $user]);
    }
}
