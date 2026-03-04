<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BoostAdd;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function categories()
    {
        $categories = getCategoriesGlobal();

        $formatted = $categories->map(function ($category) {

            $name = trim($category->categorie ?: $category->sub_categorie ?: 'Unnamed Category');

            return [
                'id'             => $category->id,
                'name'           => $name,
                'icon_url'       => $category->icon_url,

                'subcategories'  => $category->subcategories->map(function ($sub) {
                    $subName = trim($sub->sub_categorie ?: $sub->categorie ?: 'Unnamed');
                    return [
                        'id'       => $sub->id,
                        'name'     => $subName,
                        'icon_url' => $sub->icon_url,
                    ];
                })->values(),

                'specifications' => $category->specifications->map(function ($spec) {
                    return [
                        'id'            => $spec->id,
                        'specification' => $spec->specification,

                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'code'    => 0,
            'message' => $categories->isNotEmpty()
                ? 'Categories retrieved successfully'
                : 'No active categories found',
            'count'   => $categories->count(),
            'data'    => $formatted,
        ], 200);
    }

    public function freshRecommendations(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'code'    => 0,
                'message' => 'No user authenticated',
                'data'    => ['boosted' => [], 'normal' => []]
            ]);
        }

        $oneWeekAgo = Carbon::now()->subDays(7);


        $baseQuery = Product::query()
            ->with(['mainImage'])
            ->where('created_at', '>=', $oneWeekAgo)
            ->where('status', 'ACTIVE')
            ->where('active_flag', 1);

        // Favorites for current user
        $baseQuery->with(['favorites' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }]);

        // Only currently valid (non-expired) boosts for this user
        $baseQuery->with(['userProductBoosts' => function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('expired_at', '>', now())
                ->with('boostPackage')
                ->latest('expired_at');
        }]);

        $products = $baseQuery->get();


        $boostedProducts = $products->filter(fn($p) => $p->userProductBoosts->isNotEmpty());
        $normalProducts  = $products->diff($boostedProducts);


        $boostedSorted = $boostedProducts->sortByDesc(function ($product) {
            $boost = $product->userProductBoosts->first();

            $priority = match (strtolower($boost?->boostPackage?->slug ?? '')) {
                'premium'  => 300,
                'standard' => 200,
                'basic'    => 100,
                default    => 0,
            };

            return $priority * 1000000000 + $product->created_at->timestamp;
        });

        // Sort normal: newest first
        $normalSorted = $normalProducts->sortByDesc('created_at');

        // Limits — adjust as needed
        $topBoosted = $boostedSorted->take(5);
        $topNormal  = $normalSorted->take(8);

        // Shared formatting logic
        $formatProduct = function ($product) {
            $boost = $product->userProductBoosts->first();
            $boostPackage = $boost?->boostPackage;
            $isBoosted = $boost !== null;

            return [
                'id'              => $product->id,
                'title'           => $product->title,
                'price_display'   => $product->price_display ?? '₹' . number_format($product->price ?? 0, 0),
                'main_image_url'  => $product->main_image_url ?? $product->mainImage?->url ?? '',

                'location'        => $product->location ?? $product->city ?? 'Hyderabad',
                'time_ago'        => $product->created_at->diffForHumans(),

                'is_favorited'    => $product->favorites->isNotEmpty(),

                'is_boosted'      => $isBoosted,
                'boost'           => $isBoosted ? [
                    'package_title'  => $boostPackage?->title ?? 'Boosted',
                    'package_slug'   => $boostPackage?->slug ?? null,
                    'priority_label' => match (strtolower($boostPackage?->slug ?? '')) {
                        'premium'  => 'Premium Boost',
                        'standard' => 'Standard Boost',
                        'basic'    => 'Basic Boost',
                        default    => 'Boosted',
                    },
                    'expired_at'     => $product->expires_at ? $product->expires_at->toDateTimeString() : null,
                    'expires_in'     => $product->expires_at ? $product->expires_at->diffForHumans(now(), true) : null,
                ] : null,

                'status'          => $product->status, // optional — for frontend debugging
            ];
        };

        $formattedBoosted = $topBoosted->map($formatProduct)->values();
        $formattedNormal  = $topNormal->map($formatProduct)->values();

        $totalBoosted = $formattedBoosted->count();
        $totalNormal  = $formattedNormal->count();

        return response()->json([
            'code'    => 0,
            'message' => "Loaded {$totalBoosted} boosted + {$totalNormal} normal active products",
            'data'    => [
                'boosted' => $formattedBoosted,
                'normal'  => $formattedNormal,
            ],
            'stats'   => [
                'boosted_count' => $totalBoosted,
                'normal_count'  => $totalNormal,
                'total'         => $totalBoosted + $totalNormal,
            ]
        ]);
    }


    public function categoryProducts(Request $request, $categoryID)
    {
        $user = $request->user();


        $query = Product::query()
            ->where('subcategory_id', $categoryID)
            ->with(['mainImage']);


        if ($user) {
            $query->where('user_id', '!=', $user->id);
        }


        if ($city = $request->query('city')) {
            $query->where(function ($q) use ($city) {
                $q->where('city', 'like', "%{$city}%")
                    ->orWhere('location', 'like', "%{$city}%");
            });
        }


        $query->with(['userProductBoosts' => function ($q) {
            $q->where('expired_at', '>', now())
                ->with('boostPackage')
                ->latest('expired_at');
        }]);

        // Load favorites if logged in
        if ($user) {
            $query->with(['favorites' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }]);
        }

        // Sorting (applied at query level where possible)
        $sort = $request->query('sort', 'newest');
        match (strtolower($sort)) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        // Get ALL matching products
        $allProducts = $query->get();

       
        // Split: boosted = currently has active boost
        $boostedProducts = $allProducts->filter(fn($p) => $p->userProductBoosts->isNotEmpty());
        $normalProducts  = $allProducts->diff($boostedProducts);

        // ────────────────────────────────────────────────
        // Extra sorting pass (important especially for price sorting)
        $sortLower = strtolower($sort);

        if ($sortLower === 'price_low' || $sortLower === 'price_high') {
            $direction = $sortLower === 'price_high' ? SORT_DESC : SORT_ASC;

            $boostedProducts = $boostedProducts->sortBy(fn($p) => $p->price ?? 999999999, SORT_NUMERIC, $direction === SORT_DESC);
            $normalProducts  = $normalProducts->sortBy(fn($p)  => $p->price ?? 999999999, SORT_NUMERIC, $direction === SORT_DESC);
        }
        // For time-based sorts → query order is usually preserved, but we can re-ensure if needed
        else if ($sortLower === 'oldest') {
            $boostedProducts = $boostedProducts->sortBy('created_at');
            $normalProducts  = $normalProducts->sortBy('created_at');
        } else {
            $boostedProducts = $boostedProducts->sortByDesc('created_at');
            $normalProducts  = $normalProducts->sortByDesc('created_at');
        }

        // Format helper
        $formatProduct = function ($product) use ($user) {
            $activeBoost  = $product->userProductBoosts->first();
            $boostPackage = $activeBoost?->boostPackage;

            $specs = [];
            if (!empty($product->specifications) && is_array($product->specifications)) {
                foreach ($product->specifications as $key => $value) {
                    $label = ucwords(str_replace(['_', '-'], ' ', $key));
                    $specs[] = [
                        'key'   => $key,
                        'label' => $label,
                        'value' => is_array($value) ? implode(', ', $value) : (string) $value,
                    ];
                }
            }

            return [
                'id'             => $product->id,
                'title'          => $product->title,
                'price'          => number_format($product->price ?? 0, 0),
                'price_display'  => $product->price_display ?? '₹' . number_format($product->price ?? 0, 0),
                'main_image_url' => $product->main_image_url ?? $product->mainImage?->url ?? null,

                'location'       => $product->location ?? $product->city ?? 'Hyderabad',
                'time_ago'       => $product->created_at->diffForHumans(),

                'is_favorited'   => $user ? $product->favorites->isNotEmpty() : false,

                'is_boosted'     => $activeBoost !== null,
                'boost'          => $activeBoost ? [
                    'package_title'  => $boostPackage?->title ?? 'Boosted',
                    'package_slug'   => $boostPackage?->slug ?? null,
                    'priority_label' => match (strtolower($boostPackage?->slug ?? '')) {
                        'premium'  => 'Premium Boost',
                        'standard' => 'Standard Boost',
                        'basic'    => 'Basic Boost',
                        default    => 'Boosted',
                    },
                      'expired_at'     => $product->expires_at ? $product->expires_at->toDateTimeString() : null,
                    'expires_in'     => $product->expires_at ? $product->expires_at->diffForHumans(now(), true) : null,
                ] : null,

                'specifications' => $specs,
            ];
        };

        $formattedBoosted = $boostedProducts->map($formatProduct)->values();
        $formattedNormal  = $normalProducts->map($formatProduct)->values();

        $boostedCount = $formattedBoosted->count();
        $normalCount  = $formattedNormal->count();
        $total        = $boostedCount + $normalCount;

        return response()->json([
            'code'        => 0,
            'message'     => $total > 0
                ? "{$total} products found ({$boostedCount} boosted + {$normalCount} normal)"
                : 'No active products in this category',

            'category_id' => (int) $categoryID,
            'sort'        => $sort,
            'city'        => $request->query('city') ?? null,

            'data' => [
                'total'         => $total,
                'boosted_count' => $boostedCount,
                'normal_count'  => $normalCount,

                'boosted'       => $formattedBoosted,
                'normal'        => $formattedNormal,
            ],
        ]);
    }

    public function boostAdds()
    {
        $packages = BoostAdd::active()
            ->ordered()
            ->get();

        return response()->json([
            'code'    => 0,
            'message' => $packages->isNotEmpty() ? 'Boost packages loaded' : 'No active packages',
            'data'    => $packages->map(function ($pkg) {
                return [
                    'id'              => $pkg->id,
                    'title'           => $pkg->title,
                    'slug'            => $pkg->slug,
                    'price'           => number_format($pkg->price, 0),
                    'price_display'   => $pkg->price_display,       // uses accessor
                    'icon'            => $pkg->icon ? url('cloud/' . $pkg->icon) : " ",
                    'duration_days'   => $pkg->duration_days,
                    'view_multiplier' => $pkg->view_multiplier,
                    'features'        => $pkg->features ?? [],
                    'badge_text'      => $pkg->badge_text,
                    'is_popular'      => $pkg->isPopular(),
                    'sort_order'      => $pkg->sort_order,
                ];
            })->values(),
        ]);
    }
}
