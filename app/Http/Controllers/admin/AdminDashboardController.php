<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'USER')->count();
        $totalActiveadds = Product::where('status', 'ACTIVE')->count();
        $totalsoldAdds = Product::whereIn('status', ['PENDING', 'ACTIVE', 'COMPLETED'])->count();
        $totalRevenue = 5006;


        return view('admin.dashboard', compact(
            'totalUsers',
            'totalActiveadds',
            'totalsoldAdds',
            'totalRevenue',
        ));
    }

    public function getPieStats(Request $request)
    {
        $stats = DB::table('categories as c')
            ->leftJoin('products as p', 'c.id', '=', 'p.category_id')
            ->whereNull('c.parent_id')
            ->select(
                'c.categorie as name',
                DB::raw('COUNT(p.id) as count')
            )
            ->groupBy('c.id', 'c.categorie')
            ->orderByDesc('count')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'labels'  => $stats->pluck('name')->all(),
            'data'    => $stats->pluck('count')->all(),
            'total'   => $stats->sum('count'),
        ]);
    }
}
