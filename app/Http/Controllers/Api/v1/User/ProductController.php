<?php


namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\Responses;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    use Responses;

    public function getProducts(Request $request)
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'shop_id' => 'nullable|integer|exists:shops,id',
                'brand_id' => 'nullable|integer|exists:brands,id',
                'celebrity_id' => 'nullable|integer|exists:celebrities,id',
                'category_id' => 'nullable|integer|exists:categories,id',
                'search' => 'nullable|string|max:255',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'discount_only' => 'nullable|boolean',
                'is_featured' => 'nullable|in:1,2',
                'my_collabs' => 'nullable|in:1,2',
                'sort_by' => 'nullable|in:name,price,created_at,price_after_discount,updated_at',
                'sort_direction' => 'nullable|in:asc,desc',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            // Start building the query
            $query = Product::with([
                'images',
                'category',
                'celebrity',
                'brand',
                'shop',
                'variations'
            ]);

            // Apply main filters (shop_id, brand_id, celebrity_id, category_id)
            if (!empty($validated['shop_id'])) {
                $query->where('shop_id', $validated['shop_id']);
            }

            if (!empty($validated['brand_id'])) {
                $query->where('brand_id', $validated['brand_id']);
            }

            if (!empty($validated['celebrity_id'])) {
                $query->where('celebrity_id', $validated['celebrity_id']);
            }

            if (!empty($validated['category_id'])) {
                $query->where(function ($q) use ($validated) {
                    // Include products from the category itself
                    $q->where('category_id', $validated['category_id'])
                        // Include products from subcategories
                        ->orWhereHas('category', function ($subQuery) use ($validated) {
                            $subQuery->where('category_id', $validated['category_id']);
                        });
                });
            }

            // Apply search filter
            if (!empty($validated['search'])) {
                $searchTerm = $validated['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description_ar', 'LIKE', "%{$searchTerm}%")
                        // Search in related models
                        ->orWhereHas('category', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name_en', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhereHas('brand', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name_en', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhereHas('celebrity', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name_en', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhereHas('shop', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name_en', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            // Apply price filters
            if (isset($validated['min_price'])) {
                $query->where(function ($q) use ($validated) {
                    $q->where('price_after_discount', '>=', $validated['min_price'])
                        ->orWhere(function ($subQ) use ($validated) {
                            $subQ->whereNull('price_after_discount')
                                ->where('price', '>=', $validated['min_price']);
                        });
                });
            }

            if (isset($validated['max_price'])) {
                $query->where(function ($q) use ($validated) {
                    $q->where('price_after_discount', '<=', $validated['max_price'])
                        ->orWhere(function ($subQ) use ($validated) {
                            $subQ->whereNull('price_after_discount')
                                ->where('price', '<=', $validated['max_price']);
                        });
                });
            }

            // Filter for discounted products only
            if (!empty($validated['discount_only'])) {
                $query->whereNotNull('discount_percentage')
                    ->where('discount_percentage', '>', 0);
            }

            // Filter by featured status
            if (isset($validated['is_featured'])) {
                $query->where('is_featured', $validated['is_featured']);
            }

            // Filter by collaboration status
            if (isset($validated['my_collabs'])) {
                $query->where('my_collabs', $validated['my_collabs']);
            }

            // Apply sorting - DEFAULT: Newest first (created_at DESC)
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDirection = $validated['sort_direction'] ?? 'desc';

            switch ($sortBy) {
                case 'name':
                    // Sort by name (English first, then Arabic as fallback)
                    $query->orderBy('name_en', $sortDirection)
                        ->orderBy('name_ar', $sortDirection);
                    break;
                    
                case 'price':
                    // Sort by actual selling price (considering discount)
                    $query->orderByRaw("COALESCE(price_after_discount, price) {$sortDirection}")
                        ->orderBy('created_at', 'desc'); // Secondary sort by newest
                    break;
                    
                case 'price_after_discount':
                    // Sort specifically by discounted price
                    $query->orderByRaw("COALESCE(price_after_discount, price) {$sortDirection}")
                        ->orderBy('created_at', 'desc');
                    break;
                    
                case 'created_at':
                case 'updated_at':
                default:
                    // Sort by date (newest first by default)
                    $query->orderBy($sortBy, $sortDirection)
                        ->orderBy('id', 'desc'); // Secondary sort for consistent results
                    break;
            }

            // Get pagination parameters
            $perPage = $validated['per_page'] ?? 15;
            $page = $validated['page'] ?? 1;

            // Execute query with pagination
            $products = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform the data to include computed fields
            $products->getCollection()->transform(function ($product) {
                $product->final_price = $product->price_after_discount ?? $product->price;
                $product->has_discount = !is_null($product->discount_percentage) && $product->discount_percentage > 0;
                $product->savings = $product->has_discount ? ($product->price - $product->final_price) : 0;
                
                // Add formatted dates for easier frontend usage
                $product->created_at_formatted = $product->created_at->format('Y-m-d H:i:s');
                $product->updated_at_formatted = $product->updated_at->format('Y-m-d H:i:s');
                
                return $product;
            });

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more_pages' => $products->hasMorePages()
                ],
                'sorting' => [
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                    'default_sort' => 'newest_first'
                ],
                'filters_applied' => array_filter($validated) // Show which filters were applied
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function searchProduct(Request $request)
    {
        try {
            $query = Product::query();

            // Debug: Log the search term
            \Log::info('Search term: ' . $request->search);

            // Search by product name, description, or specification
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name_ar', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description_ar', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Debug: Log the SQL query
            \Log::info('SQL Query: ' . $query->toSql());
            \Log::info('Query Bindings: ', $query->getBindings());

            // Sort by price
            if ($request->has('sort_by') && $request->sort_by === 'price') {
                $sortOrder = $request->get('sort_order', 'asc'); // asc = low to high, desc = high to low

                if ($sortOrder === 'desc') {
                    $query->orderBy('price', 'desc'); // High to low
                } else {
                    $query->orderBy('price', 'asc');  // Low to high
                }
            }

            $products = $query->with('images', 'variations')->get();

            // Debug: Log results count
            \Log::info('Products found: ' . $products->count());

            // If no search term, return all products
            if (!$request->has('search') || empty($request->search)) {
                $allProducts = Product::with('images', 'variations')->get();
                return $this->success_response('All products retrieved successfully', $allProducts);
            }

            return $this->success_response('Products retrieved successfully', $products);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return $this->error_response('Error retrieving products', ['error' => $e->getMessage()]);
        }
    }

        public function productDetails($id)
    {
        // Get the main product
        $product = Product::with('images', 'ratings', 'variations', 'variations.color', 'variations.size')
            ->where('id', $id)
            ->first();
        
        if (!$product) {
            return $this->error_response('Product not found', [], 404);
        }
        
        // Get similar products from the same category (excluding the current product)
        $similarProducts = Product::with('images', 'ratings', 'variations', 'variations.color', 'variations.size')
            ->where('category_id', $product->category_id) // assuming you have a category_id field
            ->where('id', '!=', $id) // exclude the current product
            ->limit(10) // limit the number of similar products
            ->get();
        
        // Combine the data
        $response = [
            'product' => $product,
            'similar_products' => $similarProducts
        ];
        
        return $this->success_response('Product retrieved successfully', $response);
    }
}
