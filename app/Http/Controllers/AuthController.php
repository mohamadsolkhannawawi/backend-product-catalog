<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // We support combined User + Seller onboarding registration in one request.
        // Build validation rules and include DB-unique constraints where the columns exist.
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',

            // Seller onboarding fields (required for seller registration)
            'store_name'        => ['required','string','max:255'],
            'store_description' => 'nullable|string',

            'pic_name'          => 'required|string|max:255',
            'pic_phone'         => ['required','string','max:50'],

            'address'           => 'required|string|max:255',
            'rt'                => 'required|string|max:5',
            'rw'                => 'required|string|max:5',

            // Location fields: reference location tables (provinces, cities, etc.)
            // These tables have numeric 'id' (bigIncrements) and char 'code' fields
            // The laravolt/indonesia package stores code (char) in seller fields
            'province_id'       => 'required|string|exists:indonesia_provinces,code',
            'city_id'           => 'required|string|exists:indonesia_cities,code',
            'district_id'       => 'required|string|exists:indonesia_districts,code',
            'village_id'        => 'required|string|exists:indonesia_villages,code',

            'ktp_number'        => ['required','string','size:16'],

            // Files
            'pic_image'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'ktp_file'          => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ];

        // Add unique rules where appropriate
        // store_name unique on sellers.store_name
        if (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'store_name')) {
            $rules['store_name'][] = Rule::unique('sellers', 'store_name');
        }

        // pic_phone may be stored on users.phone or sellers.phone; prefer users.phone
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phone')) {
            $rules['pic_phone'][] = Rule::unique('users', 'phone');
        } elseif (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'phone')) {
            $rules['pic_phone'][] = Rule::unique('sellers', 'phone');
        }

        // KTP number uniqueness on sellers (column may be named ktp_number or nid_number)
        if (Schema::hasTable('sellers')) {
            if (Schema::hasColumn('sellers', 'ktp_number')) {
                $rules['ktp_number'][] = Rule::unique('sellers', 'ktp_number');
            } elseif (Schema::hasColumn('sellers', 'nid_number')) {
                // map input ktp_number against nid_number column
                $rules['ktp_number'][] = Rule::unique('sellers', 'nid_number');
            }
        }

        // Validate
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($request, $validated) {
            // Create user (role set to 'seller' but account remains pending until admin approves)
            $user = User::create([
            'name'     => $validated['pic_name'] ?? $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['pic_phone'] ?? null,
            'role'     => 'seller',
            ]);

            // Store files
            $ktpFile = $request->file('ktp_file');
            $ktpFilename = 'ktp_' . uniqid() . '.' . $ktpFile->getClientOriginalExtension();
            $ktpPath = $ktpFile->storeAs('documents/ktp', $ktpFilename, 'local'); // private

            $picFile = $request->file('pic_image');
            $picFilename = 'pic_' . uniqid() . '.' . $picFile->getClientOriginalExtension();
            $picPath = $picFile->storeAs('images/pic', $picFilename, 'public'); // public

            // Create seller record
            $seller = Seller::create([
            'user_id'          => $user->user_id,
            'store_name'       => $validated['store_name'],
            'store_description'=> $validated['store_description'] ?? null,

            'address'          => $validated['address'],
            'rt'               => $validated['rt'],
            'rw'               => $validated['rw'],

            'province_id'      => $validated['province_id'],
            'city_id'          => $validated['city_id'],
            'district_id'      => $validated['district_id'],
            'village_id'       => $validated['village_id'],

            'ktp_number'       => $validated['ktp_number'],
            'ktp_file_path'    => $ktpPath,
            'pic_file_path'    => $picPath,

            'status'           => 'pending',
            ]);

            // Generate Sanctum Bearer Token for immediate login after registration
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Registration submitted. Awaiting admin approval.',
                'user'    => $user,
                'seller'  => $seller,
                'token'   => $token,
            ], 201);
        });
    }

    /**
     * Onboard an already-authenticated user as a seller.
     * This consolidates previous SellerOnboardingController::store logic.
     */
    public function onboard(Request $request)
    {
        // Build similar rules as registration: include unique constraints when columns exist
        $rules = [
            'store_name'        => ['required','string','max:255'],
            'store_description' => 'nullable|string',

            'pic_name'          => 'required|string|max:255',
            'pic_phone'         => ['required','string','max:50'],

            'address'           => 'required|string|max:255',
            'rt'                => 'required|string|max:5',
            'rw'                => 'required|string|max:5',

            'province_id'       => 'required|string|exists:indonesia_provinces,code',
            'city_id'           => 'required|string|exists:indonesia_cities,code',
            'district_id'       => 'required|string|exists:indonesia_districts,code',
            'village_id'        => 'required|string|exists:indonesia_villages,code',

            'ktp_number'        => ['required','string','size:16'],

            'pic_image'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'ktp_file'          => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ];

        if (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'store_name')) {
            $rules['store_name'][] = Rule::unique('sellers', 'store_name');
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'phone')) {
            $rules['pic_phone'][] = Rule::unique('users', 'phone');
        } elseif (Schema::hasTable('sellers') && Schema::hasColumn('sellers', 'phone')) {
            $rules['pic_phone'][] = Rule::unique('sellers', 'phone');
        }

        if (Schema::hasTable('sellers')) {
            if (Schema::hasColumn('sellers', 'ktp_number')) {
                $rules['ktp_number'][] = Rule::unique('sellers', 'ktp_number');
            } elseif (Schema::hasColumn('sellers', 'nid_number')) {
                $rules['ktp_number'][] = Rule::unique('sellers', 'nid_number');
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($request, $validated) {
            // Upload KTP (private)
            $ktpFile = $request->file('ktp_file');
            $ktpFilename = 'ktp_' . uniqid() . '.' . $ktpFile->getClientOriginalExtension();
            $ktpPath = $ktpFile->storeAs('documents/ktp', $ktpFilename, 'local');

            // Upload PIC image (public)
            $picFile = $request->file('pic_image');
            $picFilename = 'pic_' . uniqid() . '.' . $picFile->getClientOriginalExtension();
            $picPath = $picFile->storeAs('images/pic', $picFilename, 'public');

            // Update user (PIC)
            $user = $request->user();
            $user->update([
                'name'  => $validated['pic_name'],
                'phone' => $validated['pic_phone'],
                'role'  => 'seller',
            ]);

            // Create seller record
            $seller = Seller::create([
                'user_id'          => $user->user_id,
                'store_name'       => $validated['store_name'],
                'store_description'=> $validated['store_description'] ?? null,

                'address'          => $validated['address'],
                'rt'               => $validated['rt'],
                'rw'               => $validated['rw'],

                'province_id'      => $validated['province_id'],
                'city_id'          => $validated['city_id'],
                'district_id'      => $validated['district_id'],
                'village_id'       => $validated['village_id'],

                'ktp_number'       => $validated['ktp_number'],
                'ktp_file_path'    => $ktpPath,
                'pic_file_path'    => $picPath,

                'status'           => 'pending',
            ]);

            return response()->json([
                'message' => 'Seller onboarding submitted',
                'seller'  => $seller,
            ], 201);
        });
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Only regenerate session if the request has a session (API requests may be stateless)
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // After successful authentication, enforce seller approval policy:
        // If the user is a seller and their seller record is not active, deny login.
        $user = Auth::user();
        if ($user && $user->role === 'seller') {
            $seller = $user->seller;
            if ($seller && $seller->status !== 'active') {
                // Log out and deny access
                Auth::guard('web')->logout();
                return response()->json(['message' => 'Account pending approval by admin'], 403);
            }
        }

        // Generate Sanctum token for API authentication
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function me(Request $request)
    {
        return Auth::user();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // If request is stateful (has session), invalidate it. API clients may be stateless.
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logged out']);
    }
}
