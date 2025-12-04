<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Return a list of all categories.
     */
    public function index(Request $request)
    {
        $categories = Category::withCount(['products as active_products_count' => function ($q) {
                if (Schema::hasColumn('products', 'is_active')) {
                    $q->where('is_active', true);
                }
            }])
            ->select('category_id', 'name', 'slug', 'description', 'icon', 'parent_id')
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                // normalize the count field name for frontend
                return [
                    'category_id' => $c->category_id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'description' => $c->description,
                    'icon' => $c->icon,
                    'parent_id' => $c->parent_id,
                    'product_count' => $c->active_products_count ?? 0,
                ];
            });

        return response()->json(['data' => $categories]);
    }
}

