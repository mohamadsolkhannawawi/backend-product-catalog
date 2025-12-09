<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\SellerApproved;
use App\Notifications\SellerRejected;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AdminSellerController extends Controller
{
    // List all sellers (with optional status filter)
    public function index(Request $request)
    {
        $q = Seller::with([
                'user:user_id,name,email',
                'province:id,name'
            ])
            ->select('seller_id', 'user_id', 'store_name', 'province_id', 'phone', 'status', 'is_active', 'updated_at', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        return $q->paginate(20);
    }

    // List pending sellers
    public function pending()
    {
        return Seller::with(['user', 'province', 'city', 'district', 'village'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // Show seller details
    public function show(Seller $seller)
    {
        return $seller->load(['user', 'province', 'city', 'district', 'village']);
    }

    // Approve seller
    public function approve(Request $request, Seller $seller)
    {
        return DB::transaction(function () use ($seller) {
            // Mark as approved by admin but require the seller to verify via email click
            $seller->status = 'approved';
            $seller->verified_at = null; // will be set when seller clicks verification link
            $seller->is_active = false;
            $seller->rejection_reason = null;
            $seller->save();

            // Generate temporary signed verification link
            $signedUrl = \URL::temporarySignedRoute(
                'seller.verify', now()->addDays(7), ['seller' => $seller->seller_id]
            );

            // Notify user with signed link
            try {
                $user = $seller->user;
                if ($user) {
                    $userKey = method_exists($user, 'getKey') ? $user->getKey() : ($user->id ?? 'unknown');
                    Log::info('Attempting to notify approved seller', ['user_key' => $userKey, 'signedUrl' => $signedUrl]);

                    // Try to send immediately (bypass queue) so email is delivered
                    // even when queue worker is not running. If sendNow fails,
                    // fallback to normal queued notification.
                    try {
                        Notification::sendNow($user, new SellerApproved([
                            'company_name' => $seller->store_name,
                        ], $signedUrl));
                        Log::info('sendNow succeeded for seller notification', ['user_key' => $userKey]);
                    } catch (\Throwable $e) {
                        Log::warning('sendNow failed, falling back to queued notify: ' . $e->getMessage(), ['user_key' => $userKey]);
                        try {
                            $user->notify(new SellerApproved([
                                'company_name' => $seller->store_name,
                            ], $signedUrl));
                            Log::info('Queued notify() called for seller notification', ['user_key' => $userKey]);
                        } catch (\Throwable $e2) {
                            Log::error('notify() failed for seller approved notification: ' . $e2->getMessage(), ['user_key' => $userKey]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send seller approved notification: ' . $e->getMessage());
            }

            return response()->json(['message' => 'Seller approved (awaiting verification)', 'seller' => $seller]);
        });
    }

    // Reject seller with optional reason
    public function reject(Request $request, Seller $seller)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $reason = $request->input('reason');
        return DB::transaction(function () use ($seller, $reason) {
            // Create an audit snapshot before deleting
            $auditData = [
                'seller_id' => $seller->seller_id ?? null,
                'user_id' => $seller->user?->user_id ?? null,
                'reason' => $reason,
                'seller_snapshot' => json_encode($seller->toArray()),
                'user_snapshot' => json_encode($seller->user?->toArray() ?? []),
            ];

            // Insert into rejection audits table
            try {
                DB::table('seller_rejection_audits')->insert([
                    'seller_id' => $auditData['seller_id'],
                    'user_id' => $auditData['user_id'],
                    'reason' => $auditData['reason'],
                    'seller_snapshot' => $auditData['seller_snapshot'],
                    'user_snapshot' => $auditData['user_snapshot'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to write seller rejection audit: ' . $e->getMessage());
            }

            // Notify user about rejection (inform them their data is removed)
            try {
                $user = $seller->user;
                if ($user) {
                    $user->notify(new SellerRejected($reason, [
                        'company_name' => $seller->store_name,
                    ]));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send seller rejected notification: ' . $e->getMessage());
            }

            // Attempt to delete files (ktp on local, pic on public)
            try {
                if ($seller->ktp_file_path && \Storage::disk('local')->exists($seller->ktp_file_path)) {
                    \Storage::disk('local')->delete($seller->ktp_file_path);
                }
                if ($seller->pic_file_path && \Storage::disk('public')->exists($seller->pic_file_path)) {
                    \Storage::disk('public')->delete($seller->pic_file_path);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to delete seller files on reject: ' . $e->getMessage());
            }

            // Delete seller and the associated user so the email can be reused
            try {
                $user = $seller->user;
                $seller->delete();
                if ($user) {
                    $user->delete();
                }
            } catch (\Throwable $e) {
                Log::error('Failed to delete seller/user after rejection: ' . $e->getMessage());
                throw $e;
            }

            return response()->json(['message' => 'Seller rejected and data removed']);
        });
    }

    /**
    /**
     * Public signed verification endpoint. When clicked from email, finalize verification.
     */
    public function verify(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link'], 403);
        }

        $sellerId = $request->query('seller');
        $seller = Seller::where('seller_id', $sellerId)->first();
        if (! $seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $seller->verified_at = now();
        $seller->is_active = true;  // ✅ Set is_active to true, not status
        // status remains 'approved' - don't change it
        $seller->save();

        // Redirect to frontend verification success page (use frontend_url, not app.url)
        $redirect = config('app.frontend_url') . '/seller/verified?seller=' . $seller->seller_id;
        return redirect($redirect);
    }

    // Stream KTP file (private disk) for admin preview
    public function ktpFile(Seller $seller)
    {
        if (!$seller->ktp_file_path) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $disk = Storage::disk('local');
        if (!$disk->exists($seller->ktp_file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $path = $disk->path($seller->ktp_file_path);
        $mime = mime_content_type($path);
        return response()->file($path, ['Content-Type' => $mime]);
    }

    // Stream PIC file (public disk) for admin preview
    public function picFile(Seller $seller)
    {
        if (!$seller->pic_file_path) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($seller->pic_file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $path = $disk->path($seller->pic_file_path);
        $mime = mime_content_type($path);
        return response()->file($path, ['Content-Type' => $mime]);
    }
}
