<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SellerOnboardingController extends Controller
{
    public function store(Request $request)
    {
        // VALIDASI 14 + 1 FIELD SESUAI SRS
        $validated = $request->validate([
            'store_name'        => 'required|string|max:255',
            'store_description' => 'nullable|string',

            'pic_name'          => 'required|string|max:255',
            'pic_phone'         => 'required|string|max:50',

            'address'           => 'required|string|max:255',
            'rt'                => 'required|string|max:5',
            'rw'                => 'required|string|max:5',

            'province_id'       => 'required|string',
            'city_id'           => 'required|string',
            'district_id'       => 'required|string',
            'village_id'        => 'required|string',

            'ktp_number'        => 'required|string|size:16',

            // FILE: Foto PIC (public)
            'pic_image'         => 'required|image|mimes:jpg,jpeg,png|max:2048',

            // FILE: Scan KTP (private)
            'ktp_file'          => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated) {

            // 1️. UPLOAD KTP: PRIVATE STORAGE (SRS: storage/app/documents/ktp)
            $ktpFile = $request->file('ktp_file');
            $ktpFilename = 'ktp_' . uniqid() . '.' . $ktpFile->getClientOriginalExtension();
            $ktpPath = $ktpFile->storeAs('documents/ktp', $ktpFilename, 'local'); // private

            // 2️. UPLOAD FOTO PIC: PUBLIC STORAGE (SRS: profil/produk di public)
            $picFile = $request->file('pic_image');
            $picFilename = 'pic_' . uniqid() . '.' . $picFile->getClientOriginalExtension();
            $picPath = $picFile->storeAs('images/pic', $picFilename, 'public'); // public

            // 3️. UPDATE DATA USER (PIC): name, phone, role
            $user = $request->user();
            $user->update([
                'name'  => $validated['pic_name'],
                'phone' => $validated['pic_phone'],
                'role'  => 'seller', // atau 'seller'/'buyer'
            ]);

            // 4️. BUAT RECORD SELLER
            $seller = Seller::create([
                'user_id'          => $user->id,
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
}
