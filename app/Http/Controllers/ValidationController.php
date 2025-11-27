<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;

class ValidationController extends Controller
{
    /**
     * Simple uniqueness check used by frontend async validation.
     * Expects JSON: { field: string, value: string }
     * Returns: { exists: boolean }
     */
    public function unique(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        if (empty($field) || is_null($value)) {
            return response()->json(['exists' => false]);
        }

        $exists = false;

        switch ($field) {
            case 'email':
                $exists = User::where('email', $value)->exists();
                break;
            case 'store_name':
                $exists = Seller::where('store_name', $value)->exists();
                break;
            case 'pic_phone':
            case 'phone':
                $exists = Seller::where('phone', $value)->exists();
                break;
            case 'ktp_number':
                $exists = Seller::where('ktp_number', $value)->exists();
                break;
            default:
                $exists = false;
        }

        return response()->json(['exists' => (bool) $exists]);
    }
}
