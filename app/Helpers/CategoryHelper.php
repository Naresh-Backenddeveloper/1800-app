<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

if (!function_exists('getCategoriesGlobal')) {

    function getCategoriesGlobal()
    {
        return Cache::remember('api_categories_nested_v3', now()->addMinutes(10), function () {
            $topCategories = Category::query()
                ->whereNull('parent_id')
                ->active()
                ->with(['subcategories' => function ($query) {
                    $query->active()
                        ->select('id', 'categorie', 'sub_categorie', 'parent_id', 'category_icon', 'active_flag')
                        ->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'asc')
                ->get();

            // Manually load specifications for top-level categories only (no model)
            $topIds = $topCategories->pluck('id');

            $allSpecs = DB::table('specifications')
                ->whereIn('category_id', $topIds)
                ->where('active_flag', 1)
                ->select('id', 'category_id', 'specification')
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy('category_id');

            // Attach specs to each category
            $topCategories->each(function ($cat) use ($allSpecs) {
                $cat->specifications = $allSpecs->get($cat->id, collect());
            });

            return $topCategories;
        });
    }
}
