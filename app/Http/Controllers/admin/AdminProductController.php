<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['user', 'category', 'subcategory'])
            ->latest();


        if ($search = trim($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ad_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }


        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where('category_id', $category);
            }
        }



        $products = $query->paginate(15)->withQueryString();


        $stats = [
            'total'    => Product::count(),
            'pending'  => Product::where('status', 'pending')->count(),
            'active'   => Product::where('status', 'active')->count(),
            'reported' => Product::where('status', 'reported')->count(),

        ];

        $categories = Category::whereNull('parent_id')
            ->orderBy('categorie')
            ->get();

        return view('admin.products.products', compact('products', 'stats', 'categories'));
    }
}
