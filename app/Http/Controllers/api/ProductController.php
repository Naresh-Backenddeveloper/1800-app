<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BoostAdd;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserProductBoost;
use App\Services\Upload_Images;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\SystemNotificationService;

class ProductController extends Controller
{

    protected $uploadImages;
    protected $notificationService;

    public function __construct(Upload_Images $uploadImages, SystemNotificationService $notificationService)
    {
        $this->uploadImages = $uploadImages;
        $this->notificationService = $notificationService;
    }

    public function myProducts(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'code'    => 401,
                'message' => 'Please login to view your ads',
                'data'    => ['boosted' => [], 'normal' => []]
            ], 401);
        }

        $query = Product::where('user_id', $user->id)
            ->with(['mainImage'])
            ->withCount(['favorites'])
            ->with(['userProductBoosts' => function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('expired_at', '>', now())
                    ->with('boostPackage')
                    ->latest('expired_at');
            }]);


        $tab = strtolower($request->query('tab', 'all'));
        match ($tab) {
            'active'  => $query->where('status', 'ACTIVE'),
            'pending' => $query->where('status', 'PENDING'),
            'sold'    => $query->where('status', 'SOLD'),
            'expired' => $query->where(function ($q) {
                $q->where('expires_at', '<', now())->orWhereNull('expires_at');
            }),
            default   => $query,
        };

        $allProducts = $query->get();


        $boostedProducts = $allProducts->filter(fn($p) => $p->userProductBoosts->isNotEmpty());
        $normalProducts  = $allProducts->diff($boostedProducts);


        $boostedProducts = $boostedProducts->sortBy(function ($product) {
            $boost = $product->userProductBoosts->first();
            $priority = match (strtolower($boost?->boostPackage?->slug ?? '')) {
                'premium'  => 1,
                'standard' => 2,
                'basic'    => 3,
                default    => 4,
            };
            return [$priority, $product->created_at->timestamp * -1];
        })->values();


        $normalProducts = $normalProducts->sortByDesc('created_at')->values();


        $formatProduct = function ($product) use ($user) {
            $activeBoost  = $product->userProductBoosts->first();
            $boostPackage = $activeBoost?->boostPackage;
            $isBoosted    = $activeBoost !== null;

            return [
                'id'              => $product->id,
                'title'           => $product->title,
                'slug'            => $product->slug,
                'price'           => (float) $product->price,
                'price_display'   => ($product->currency ?? '₹') . ' ' . number_format($product->price ?? 0, 0),
                'main_image_url'  => $product->main_image_url
                    ? url('cloud/' . $product->main_image_url)
                    : null,

                'status'          => ucfirst($product->status ?? 'unknown'),
                'status_color'    => match (strtolower($product->status ?? '')) {
                    'active'   => '#22c55e',
                    'pending'  => '#f59e0b',
                    'sold'     => '#6b7280',
                    default    => '#6b7280',
                },

                'views_count'     => (int) ($product->views_count ?? 0),
                'favorites_count' => (int) ($product->favorites_count ?? 0),

                'posted_at'       => $product->created_at?->toDateTimeString(),
                'posted_ago'      => $product->created_at?->diffForHumans(),

                'is_boosted'      => $isBoosted,
                'boost'           => $isBoosted ? [
                    'package_title'  => $boostPackage?->title ?? 'Unknown',
                    'package_slug'   => $boostPackage?->slug ?? null,
                    'price_paid'     => number_format($activeBoost->price ?? 0, 2),
                    'expired_at'     => $product->expires_at ? $product->expires_at->toDateTimeString() : null,
                    'expires_in'     => $product->expires_at ? $product->expires_at->diffForHumans(now(), true) : 'EXPIRED',
                    'boost_priority' => match (strtolower($boostPackage?->slug ?? '')) {
                        'premium'  => 'high',
                        'standard' => 'medium',
                        'basic'    => 'low',
                        default    => 'unknown',
                    },
                ] : null,

                'location'        => $product->location ?? $product->city ?? 'Hyderabad',
                'condition'       => $product->condition ?? null,
                'year'            => $product->year ?? null,
            ];
        };

        $formattedBoosted = $boostedProducts->map($formatProduct)->values();
        $formattedNormal  = $normalProducts->map($formatProduct)->values();

        $boostedCount = $formattedBoosted->count();
        $normalCount  = $formattedNormal->count();
        $total        = $boostedCount + $normalCount;


        $countQueryBase = Product::where('user_id', $user->id);

        $counts = [
            'all'     => (clone $countQueryBase)->count(),
            'boosted' => $boostedCount,
            'normal'  => $normalCount,
            'active'  => (clone $countQueryBase)->where('status', 'active')->where('active_flag', 1)->count(),
            'pending' => (clone $countQueryBase)->where('status', 'pending')->count(),
            'sold'    => (clone $countQueryBase)->where('status', 'sold')->count(),
            'expired' => (clone $countQueryBase)->where(function ($q) {
                $q->where('expires_at', '<', now())->orWhereNull('expires_at');
            })->count(),
        ];
        $tabBoostedCount = $tab === 'all' ? $boostedCount : $formattedBoosted->count();
        $tabNormalCount  = $tab === 'all' ? $normalCount  : $formattedNormal->count();

        return response()->json([
            'code'       => 0,
            'message'    => $total > 0
                ? "{$total} products loaded ({$tabBoostedCount} boosted + {$tabNormalCount} normal)"
                : 'No products found',


            'data'       => [
                'tab'        => $tab,
                'counts'     => $counts,
                'total'      => $total,
                'boosted' => $formattedBoosted,
                'normal'  => $formattedNormal,
            ],
        ]);
    }

    public function addPost(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'product_images'     => 'required|array|min:1|max:10',
            'product_images.*'   => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'title'              => 'required|string|max:255',
            'category_id'        => 'required',
            'sub_category_id'    => 'nullable',
            'specifications'     => 'required|json',
            'condition'          => 'required',
            'description'        => 'required|string|max:5000',
            'price'              => 'required|numeric|min:0',
            'price_negotiable'   => 'required|boolean',
            'location'           => 'required|string|max:255',
            'city'               => 'nullable|string|max:100',
            'latitude'           => 'nullable|numeric',
            'longitude'          => 'nullable|numeric',
            'year'               => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'    => 1,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()->first()
            ], 422);
        }

        $product = Product::create([
            'user_id'            => $user->id,
            'category_id'        => $request->category_id,
            'subcategory_id'     => $request->sub_category_id ?? null,
            'title'              => $request->title,
            'description'        => $request->description,
            'specifications'     => $request->specifications,
            'price'              => $request->price,
            'price_negotiable'   => $request->price_negotiable ? 1 : 0,
            'currency'           => 'INR',
            'condition'          => $request->condition,
            'year'               => $request->year ?? null,
            'location'           => $request->location,
            'city'               => $request->city ?? 'Hyderabad',
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'status'             => 'PENDING',
            'active_flag'        => 0,
            'created_at'         => Carbon::now(),
            'updated_at'         => Carbon::now(),
        ]);

        // 3. Handle image uploads
        $uploadedCount = 0;
        $firstImageSet = false;

        if ($request->hasFile('product_images')) {
            $images = $request->file('product_images');

            foreach ($images as $index => $image) {

                $relativePath = $this->uploadImages->storageFilepath($image);

                if ($relativePath) {
                    ProductImage::create([
                        'product_id'    => $product->id,
                        'url'           => $relativePath,
                        'is_main'       => !$firstImageSet ? 1 : 0,
                        'order'         => $index,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);

                    $uploadedCount++;
                    $firstImageSet = true;
                }
            }
        }

        $this->notificationService->notifySeller('PRODUCT_SUBMITTED', $user->id);

        return response()->json([
            'code'    => 0,
            'message' => $uploadedCount > 0
                ? "Ad posted successfully with {$uploadedCount} image(s). Under review."
                : 'Ad posted successfully (no images uploaded). Under review.',
            'data'    => [
                'product_id' => $product->id,
                'title'      => $product->title,
                'status'     => $product->status,
            ]
        ], 201);
    }

    public function productDetail(Request $request, $productId)
    {
        $user = $request->user();

        $product = Product::query()
            ->where('id', $productId)
            ->with([
                'mainImage',
                'images',
                'category',
                'user',

                'userProductBoosts' => function ($q) {
                    $q->where('expired_at', '>', now())
                        ->with('boostPackage')
                        ->latest('expired_at');
                },
            ])
            ->first();

        if (!$product) {
            return response()->json([
                'code'    => 1,
                'message' => 'Product not found or inactive',
                'data'    => null
            ], 404);
        }


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
        $allImages = $product->images
            ->map(function ($image) {
                return [
                    'id'  => $image->id,
                    'url' => $image->url ? url('cloud/' . $image->url) : null,
                    'is_main' => (bool) ($image->is_main ?? false),

                ];
            })
            ->values();

        $activeBoost   = $product->userProductBoosts->first();
        $boostPackage  = $activeBoost?->boostPackage;
        $isBoosted     = $activeBoost !== null;


        $isFavorited = $user
            ? $product->favorites()->where('user_id', $user->id)->exists()
            : false;

        $formatted = [
            'id'              => $product->id,
            'title'           => $product->title,
            'description'     => $product->description ?? '',
            'price'           => number_format($product->price ?? 0, 0),
            'price_display'   => $product->price_display ?? '₹' . number_format($product->price ?? 0, 0),
            'category_id'     => $product->category_id,
            'category_name'   => $product->category?->categorie ?? $product->category?->name ?? null,

            'main_image_url'  => $product->main_image_url ?? null,
            'images'          => $allImages,
            'location'        => $product->location ?? $product->city ?? 'Hyderabad',
            'city'            => $product->city ?? null,

            'sellerName'      => $product->user?->name ?? 'Unknown Seller',
            'sellerPhone'     => $product->user?->mobile ?? null,
            'sellerWhatsapp'  => $product->user?->mobile ?? null,
            'sellerEmail'     => $product->user?->email ?? null,

            'condition'       => $product->condition ?? null,
            'negotiable'      => $product->price_negotiable ?? $product->negotiable ?? false,

            'specifications'  => $specs,

            'created_at'      => $product->created_at->toDateTimeString(),
            'time_ago'        => $product->created_at->diffForHumans(),

            'views'           => (int) ($product->views ?? $product->views_count ?? 0),

            'is_favorited'    => $isFavorited,

            'is_boosted'      => $isBoosted,
            'boost'           => $isBoosted ? [
                'package_title'  => $boostPackage?->title ?? 'Boosted Ad',
                'package_slug'   => $boostPackage?->slug ?? null,
                'priority_label' => match (strtolower($boostPackage?->slug ?? '')) {
                    'premium'  => 'Premium Boost',
                    'standard' => 'Standard Boost',
                    'basic'    => 'Basic Boost',
                    default    => 'Boosted',
                },
                'expired_at'     => $product->expires_at?->toDateTimeString(),
                'expires_in'     => $product->expires_at
                    ? $product->expires_at->diffForHumans(now(), true) . ' left'
                    : 'Expired',
                'expires_in_days' => $product->expires_at?->isFuture()
                    ? round(now()->diffInDays($product->expires_at), 0) . ' days left'
                    : 'Expired',
            ] : null,
        ];

        return response()->json([
            'code'    => 0,
            'message' => 'Product details retrieved successfully',
            'data'    => $formatted
        ]);
    }

    public function deletePostImages($ImageId)
    {
        $result = DB::table('product_images')->where('id', $ImageId)->delete();
        return response()->json([
            'code'    => 0,
            'message' => 'Product Image Removed'
        ]);
    }

    public function addImages(Request $request, $productId)
    {

        $validator = Validator::make($request->all(), [
            'product_images'     => 'required|array|min:1|max:10',
            'product_images.*'   => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'    => 1,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }


        $product = Product::where('id', $productId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$product) {
            return response()->json([
                'code'    => 1,
                'message' => 'Product not found or you do not have permission',
            ], 404);
        }


        $hasMainImage = ProductImage::where('product_id', $productId)
            ->where('is_main', 1)
            ->exists();

        $uploadedCount = 0;
        $images = $request->file('product_images');


        $nextOrder = ProductImage::where('product_id', $productId)
            ->max('order') ?? -1;
        $nextOrder++;

        foreach ($images as $index => $image) {
            $relativePath = $this->uploadImages->storageFilepath($image);

            if ($relativePath) {
                $isMain = false;


                if (!$hasMainImage && $uploadedCount === 0) {
                    $isMain = true;
                }

                ProductImage::create([
                    'product_id'    => $productId,
                    'url'           => $relativePath,
                    'is_main'       => $isMain ? 1 : 0,
                    'order'         => $nextOrder + $index,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);

                $uploadedCount++;
            }
        }


        if ($uploadedCount === 0) {
            return response()->json([
                'code'    => 1,
                'message' => 'No valid images were uploaded',
            ], 422);
        }

        // Success response
        return response()->json([
            'code'    => 0,
            'message' => "Successfully added {$uploadedCount} new image(s)",
            'data'    => [
                'product_id'     => $productId,
                'images_added'   => $uploadedCount,
                'has_main_image' => ProductImage::where('product_id', $productId)
                    ->where('is_main', 1)
                    ->exists(),
            ]
        ], 201);
    }

    public function editPost(Request $request, $productId)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title'              => 'required',
            'category_id'        => 'required',
            'sub_category_id'    => 'required',
            'specifications'     => 'required',
            'condition'          => 'required',
            'description'        => 'required',
            'price'              => 'required',
            'price_negotiable'   => 'required',
            'location'           => 'required',
            'city'               => 'nullable',
            'latitude'           => 'nullable',
            'longitude'          => 'nullable',
            'year'               => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'    => 1,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()->first()
            ], 422);
        }

        $product = Product::where('id', $productId)->update([
            'user_id'            => $user->id,
            'category_id'        => $request->category_id,
            'subcategory_id'     => $request->sub_category_id ?? null,
            'title'              => $request->title,
            'description'        => $request->description,
            'specifications'     => $request->specifications,
            'price'              => $request->price,
            'price_negotiable'   => $request->price_negotiable ? 1 : 0,
            'currency'           => 'INR',
            'condition'          => $request->condition,
            'year'               => $request->year ?? null,
            'location'           => $request->location,
            'city'               => $request->city ?? 'Hyderabad',
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'updated_at'         => Carbon::now(),
        ]);
        if ($product) {
            return response()->json([
                'code'    => 0,
                'message' => 'Product Updated'
            ], 201);
        }
        return response()->json([
            'code'    => 1,
            'message' => 'Invalid'
        ]);
    }

    public function myFavoriteProducts(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'code'    => 401,
                'message' => 'Please login to view your favorite products',
                'data'    => ['boosted' => [], 'normal' => []]
            ], 401);
        }

        $favoriteProducts = Product::query()
            ->whereIn('id', function ($q) use ($user) {
                $q->select('product_id')
                    ->from('favorites')
                    ->where('user_id', $user->id);
            })
            ->with(['mainImage'])
            ->with(['userProductBoosts' => function ($q) {
                $q->where('expired_at', '>', now())
                    ->with('boostPackage')
                    ->latest('expired_at');
            }])
            ->get();


        $boostedProducts = $favoriteProducts->filter(function ($product) {
            return $product->userProductBoosts->isNotEmpty();
        });

        $normalProducts = $favoriteProducts->diff($boostedProducts);


        $boostedProducts = $boostedProducts->sortBy(function ($product) {
            $boost = $product->userProductBoosts->first();
            $priority = match (strtolower($boost?->boostPackage?->slug ?? '')) {
                'premium'  => 1,
                'standard' => 2,
                'basic'    => 3,
                default    => 4,
            };
            return [$priority, $product->created_at->timestamp * -1];
        })->values();


        $normalProducts = $normalProducts->sortByDesc('created_at')->values();


        $formatProduct = function ($product) use ($user) {
            $activeBoost  = $product->userProductBoosts->first();
            $boostPackage = $activeBoost?->boostPackage;
            $isBoosted    = $activeBoost !== null;

            return [
                'id'              => $product->id,
                'title'           => $product->title,
                'price'           => (float) ($product->price ?? 0),
                'price_display'   => $product->price_display ?? '₹' . number_format($product->price ?? 0, 0),
                'main_image_url'  => $product->main_image_url ?? $product->mainImage?->url ?? null,

                'location'        => $product->location ?? $product->city ?? 'Hyderabad',
                'time_ago'        => $product->created_at?->diffForHumans(),

                'is_favorited'    => true,
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
                    'expires_in'     => $product->expires_at ? $product->expires_at->diffForHumans(now(), true) : 'EXPIRED',
                ] : null,

                'status'          => ucfirst($product->status ?? 'unknown'),
                'condition'       => $product->condition ?? null,
                'year'            => $product->year ?? null,

                // Optional extra fields you might want
                'views_count'     => (int) ($product->views_count ?? 0),
            ];
        };

        $formattedBoosted = $boostedProducts->map($formatProduct)->values();
        $formattedNormal  = $normalProducts->map($formatProduct)->values();

        $boostedCount = $formattedBoosted->count();
        $normalCount  = $formattedNormal->count();
        $total        = $boostedCount + $normalCount;

        return response()->json([
            'code'    => 0,
            'message' => $total > 0
                ? "Loaded {$total} favorite products ({$boostedCount} boosted + {$normalCount} normal)"
                : 'No favorite products found',

            'total'         => $total,
            'boosted_count' => $boostedCount,
            'normal_count'  => $normalCount,

            'data' => [
                'boosted' => $formattedBoosted,
                'normal'  => $formattedNormal,
            ],
        ]);
    }

    public function makeFavorite(Request $request, $productId)
    {
        $user = $request->user();

        $data = [
            'user_id' => $user->id,
            'product_id' => $productId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $result = DB::table('favorites')->insert($data);
        if ($result) {
            return response()->json([
                'code' => 0,
                'meassge' => 'OK'
            ]);
        }
        return response()->json([
            'code' => 1,
            'meassge' => 'Invalid'
        ]);
    }

    public function removeFavourite(Request $request, $productId)
    {
        $user = $request->user();
        $result = DB::table('favorites')->where('user_id', $user->id)->where('product_id', $productId)->delete();
        if ($result) {
            return response()->json([
                'code' => 0,
                'meassge' => 'OK'
            ]);
        }
        return response()->json([
            'code' => 1,
            'meassge' => 'Invalid'
        ]);
    }

    public function subscriptions()
    {
        $data = BoostAdd::where('active_flag', '1')->get();

        return response()->json([
            'code'    => 0,
            'message' => 'OK',
            'data' => $data
        ]);
    }

    public function makeProductBoost(Request $request, $productId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'code'    => 1,
                'message' => 'Unauthorized - please login',
            ], 401);
        }

        $validated = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:boost_adds,id',
            'price'        => 'required|numeric|min:1',
        ])->validate();

        $boostPackage = BoostAdd::where('id', $request->subscription_id)
            ->where('active_flag', 1)
            ->first();

        if (!$boostPackage) {
            return response()->json([
                'code'    => 1,
                'message' => 'Selected boost package not found or inactive',
            ], 404);
        }

        if ((float) $boostPackage->price !== (float) $request->price) {
            return response()->json([
                'code'    => 1,
                'message' => 'Price mismatch - possible tampering',
            ], 400);
        }

        $product = Product::where('id', $productId)
            ->where('user_id', $user->id)
            ->first();

        if (!$product) {
            return response()->json([
                'code'    => 1,
                'message' => 'Product not found or does not belong to you',
            ], 403);
        }

        // ────────────────────────────────────────────────
        // Simulate payment (replace with real gateway in production)
        // ────────────────────────────────────────────────
        $paymentSuccessful = true; // ← REPLACE WITH ACTUAL PAYMENT VERIFICATION

        if (!$paymentSuccessful) {
            return response()->json([
                'code'    => 1,
                'message' => 'Payment failed or was not completed',
            ], 402);
        }

        // ────────────────────────────────────────────────
        // Calculate new expiry duration
        // ────────────────────────────────────────────────
        $durationDays = (int) $boostPackage->duration_days;

        if ($durationDays <= 0) {
            return response()->json([
                'code'    => 1,
                'message' => 'Invalid duration for this boost package',
            ], 400);
        }

        // ────────────────────────────────────────────────
        // Transaction block
        // ────────────────────────────────────────────────
        DB::beginTransaction();

        try {
            // Check if there is already an ACTIVE boost for this product + user
            $existingBoost = UserProductBoost::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('expired_at', '>', now())
                ->latest('expired_at')           // take the one that lasts longest
                ->first();

            $actionType = 'new';

            if ($existingBoost) {
                // ─── EXTEND existing boost ───
                $newExpiredAt = $existingBoost->expired_at->addDays($durationDays);

                $existingBoost->update([
                    'boost_add_id'   => $boostPackage->id,
                    'price'          => $boostPackage->price,
                    'expired_at'     => $newExpiredAt,
                    'updated_at'     => now(),
                    'slug'           => $boostPackage->slug, // optional
                ]);

                $finalExpiredAt = $newExpiredAt;
                $actionType = 'extended';
            } else {
                // ─── Create new boost ───
                $finalExpiredAt = Carbon::now()->addDays($durationDays);

                UserProductBoost::create([
                    'user_id'        => $user->id,
                    'product_id'     => $product->id,
                    'boost_add_id'   => $boostPackage->id,
                    'price'          => $boostPackage->price,
                    'expired_at'     => $finalExpiredAt,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                    'slug'           => $boostPackage->slug,
                ]);

                $actionType = 'activated';
            }

            DB::commit();

            $message = match ($actionType) {
                'extended'  => 'Boost extended successfully!',
                'activated' => 'Boost activated successfully!',
                default     => 'Boost processed successfully!',
            };

            return response()->json([
                'code'    => 0,
                'message' => $message,
                'data'    => [
                    'product_id'     => $product->id,
                    'package_title'  => $boostPackage->title,
                    'package_slug'   => $boostPackage->slug,
                    'price'          => $boostPackage->price,
                    'duration_days'  => $boostPackage->duration_days,
                    'expired_at'     => $finalExpiredAt->toDateTimeString(),
                    'expires_in'     => $finalExpiredAt->diffForHumans(now(), true) . ' left',
                    'action'         => $actionType, // 'new', 'extended'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'code'    => 1,
                'message' => 'Failed to process boost: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function productChat(Request $request, $productId)
    {
        $user = $request->user();

        $product = Product::findOrFail($productId);


        $chat = Chat::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'product_id' => $productId,
                'user_id'    => $user->id,
            ]);
        }

        return response()->json([
            'code'    => 0,
            'message' => 'OK',
            'chatId'  => $chat->id,
            'sellerId' => $product->user_id,
            'productTitle' => $product->title ?? $product->name ?? 'Product',
        ]);
    }

    public function messages($chatId)
    {
        $user = request()->user();

        $chat = Chat::with('product')->findOrFail($chatId);


        $product = $chat->product;
        if ($chat->user_id !== $user->id && $product->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'id'         => $msg->id,
                'sender_id'  => $msg->sender_id,
                'isMine'     => $msg->sender_id === $user->id,
                'message'    => $msg->message,
                'created_at' => $msg->created_at,
                'read'       => $msg->is_read,
            ]);

        return response()->json([
            'code'     => 0,
            'chat'     => [
                'id'       => $chat->id,
                'buyer_id' => $chat->user_id,
                'seller_id' => $product->user_id,
            ],
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, $chatId)
    {
        $user = $request->user();


        $chat = Chat::with('product')->find($chatId);

        if (!$chat) {
            return response()->json([
                'code'    => 1,
                'message' => 'Chat not found'
            ], 404);
        }


        $product = $chat->product;
        $isParticipant = ($chat->user_id === $user->id) || ($product->user_id === $user->id);

        if (!$isParticipant) {
            return response()->json([
                'code'    => 1,
                'message' => 'You are not part of this chat'
            ], 403);
        }


        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'    => 1,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }


        $receiverId = ($chat->user_id === $user->id)
            ? $product->user_id
            : $chat->user_id;


        $message = Message::create([
            'chat_id'     => $chat->id,
            'sender_id'   => $user->id,
            'receiver_id' => $receiverId,
            'message'     => $request->message,
            'read_flag'   => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $chat->touch();

        return response()->json([
            'code'    => 0,
            'message' => 'Message sent successfully',
            'data'    => [
                'id'          => $message->id,
                'chat_id'     => $message->chat_id,
                'sender_id'   => $message->sender_id,
                'is_mine'     => true,
                'message'     => $message->message,
                'created_at'  => $message->created_at->toIso8601String(),
                'read'        => false,
            ]
        ]);
    }

    public function myChats(Request $request)
    {
        $user = $request->user();

        $query = Chat::query()
            ->with([
                'product' => fn($q) => $q->select('id', 'title', 'user_id', 'price'),
                'product.user' => fn($q) => $q->select('id', 'name'),
                'messages' => fn($q) => $q->latest('created_at')->limit(1),
            ])
            ->withCount('messages')
            ->orderBy('updated_at', 'desc');

        $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)                           // I'm buyer
                ->orWhereIn('product_id', $user->products()->select('id')); // I'm seller
        });

        $chats = $query->get()
            ->map(function ($chat) use ($user) {
                $last = $chat->messages->first();

                $isBuyer = $chat->user_id === $user->id;

                $otherUser = $isBuyer
                    ? $chat->product->user
                    : $chat->buyer;

                $role = $isBuyer ? 'as_buyer' : 'as_seller';

                return [
                    'chat_id'      => $chat->id,
                    'type'         => $role,
                    'product'      => [
                        'id'    => $chat->product->id,
                        'title' => $chat->product->title ?? 'Product',
                    ],
                    'seller'   => [
                        'id'   => $otherUser?->id ?? null,
                        'name' => $otherUser?->name ?? ($isBuyer ? 'Seller' : 'Buyer'),
                    ],
                    'last_message' => $last?->message,
                    'last_time'    => $last
                        ? $last->created_at->diffForHumans()
                        : $chat->created_at->diffForHumans(),
                    'updated_at'   => $chat->updated_at,
                ];
            })
            ->unique('chat_id')
            ->values();

        return response()->json([
            'code'  => 0,
            'chats' => $chats,
            'stats' => [
                'total'     => $chats->count(),
                'as_buyer'  => $chats->where('type', 'as_buyer')->count(),
                'as_seller' => $chats->where('type', 'as_seller')->count(),
            ]
        ]);
    }

    public function productrequestUsers(Request $request, $productId)
    {
        $owner = $request->user();

        $product = Product::where('id', $productId)
            ->where('user_id', $owner->id)
            ->select('id', 'title', 'user_id')
            ->first();

        if (!$product) {
            return response()->json([
                'code'    => 1,
                'message' => 'Product not found or you do not own this product'
            ], 403);
        }


        $users = Chat::where('product_id', $productId)
            ->join('users', 'chats.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('MIN(chats.created_at) as first_contact_at')
            )
            ->distinct('users.id')
            ->groupBy('users.id', 'users.name')
            ->orderBy('first_contact_at', 'desc')
            ->get();


        return response()->json([
            'code'          => 0,
            'message'       => 'Success',
            'data' => [
                'product'       => [
                    'id'    => $product->id,
                    'title' => $product->title ?? $product->name ?? 'Untitled Product',
                ],
                'interested_users' => $users->map(function ($user) {
                    return [
                        'user_id' => $user->id,
                        'name'    => $user->name ?? 'User #' . $user->id,
                    ];
                })->values(),
                'count' => $users->count(),
            ]


        ]);
    }

    public function productSold(Request $request, $productid)
    {
        $owner = $request->user();
        $product = Product::where('id', $productid)
            ->where('user_id', $owner->id)
            ->first();

        if (!$product) {
            return response()->json([
                'code'    => 1,
                'message' => 'Product not found or you are not the owner'
            ], 403);
        }

        if ($product->status === 'sold' || $product->active_flag == 0) {
            return response()->json([
                'code'    => 1,
                'message' => 'This product is already marked as sold'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'sold_to' => 'required|integer|exists:users,id',
            'rating' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'    => 1,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $buyerId = $request->input('sold_to');

        if ($buyerId == $owner->id) {
            return response()->json([
                'code'    => 1,
                'message' => 'You cannot mark yourself as the buyer'
            ], 400);
        }

        $product->update([
            'status'      => 'SOLD',
            'active_flag' => 0,
            'sold_to'     => $buyerId,
            'buyer_rating' => $request->rating,
            'sold_at'     => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
        return response()->json([
            'code'    => 0,
            'message' => 'Product marked as sold successfully',
            'data'    => [
                'product_id' => $product->id,
                'title'      => $product->title ?? $product->name ?? 'Product',
                'sold_to'    => $buyerId,
                'sold_at'    => $product->sold_at?->toDateTimeString(),
                'status'     => $product->status,
                'active_flag' => $product->active_flag,
            ]
        ]);
    }
}
