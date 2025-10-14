<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\ProductImageResource;
use Psy\CodeCleaner\IssetPass;

class ProductController extends BaseController
{
    //listing
    public function listing(Request $request)
    {
        $request->validate([
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);

        $query = Product::active()->with('brand', 'image', 'category', 'variations');

        if (isset($request->category_id)) {
            $query = $query->where('category_id', $request->category_id);
        }

        if (isset($request->sub_category_id)) {
            $query = $query->where('sub_category_id', $request->sub_category_id);
        }





        if (isset($request->brand_id)) {
            $query = $query->where('brand_id', $request->brand_id);
        }

        // if (isset($request->min_price)) {
        //     $query = $query->where('price', '>=', (int)$request->min_price);
        // }

          $min = (int)$request->input('min_price');
        $max = (int)$request->input('max_price');

        // if (isset($request->min_price) && isset($request->max_price)) {
        //    $singleProduct=Product::where('product_type',1);
        //    $variableProducts=Variation::whereIn('product_id',$singleProduct)
        // }

        // $min = $request->min_price ?? 0;
        // $max = $request->max_price ?? 10000000;

        // $query = $query->leftJoin('product_variations', 'products.id', '=', 'product_variations.product_id')
        // ->where(function ($q) use ($min, $max) {
        //     $q->where(function ($subQuery) use ($min, $max) {
        //         $subQuery->where('products.product_type', 1)
        //                  ->where('products.price', '>=', $min)
        //                  ->where('products.price', '<=', $max);
        //     })->orWhere(function ($subQuery) use ($min, $max) {
        //         $subQuery->where('products.product_type', 2)
        //                  ->whereRaw("CAST(product_variations.price AS UNSIGNED) BETWEEN ? AND ?", [$min, $max]);
        //     });
        // });

        //  if (isset($request->min_price) && !isset($request->max_price)) {
        //     $query = $query->where('price', '>=', (int)$request->min_price);
        // }

        // if (isset($request->max_price)) {
        //     $query = $query->where('price', '<=', (int)$request->max_price);
        // }

        if (isset($request->search_key)) {
            $query = $query->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->search_key . '%')
                    ->orWhereHas('brand', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search_key . '%');
                    });
            });
        }

        // $result = $query->orderBy('products.id', 'desc')->paginate($request->limit);
        // if($request->min_price || $request->max_price) {
        //     $result->items = collect($result->items())->filter(function ($item) use ($request) {
        //         if($item->variations != []) {
        //             $prices = isset($item->variations) ?  json_decode($item->variations->price, true) : [];
        //             foreach ($prices as $price) {
        //                 if ($price >= $request->min_price && $price <= $request->max_price) {
        //                     return true;
        //                 }
        //             }
        //         }
        //     });
        // }

        $result = $query->orderBy('products.id', 'desc')->paginate($request->limit);

         if (isset($request->min_price) && isset($request->max_price)) {
             $filteredProducts = $result->filter(function ($product) use ($request) {
                if ($product->product_type == 1) {
                    if ($product->discount_price) {
                        $discount_price=(int)$product->discount_price;
                          return $discount_price >= $request->min_price && $discount_price <= $request->max_price;
                    }
                    return $product->price >= $request->min_price && $product->price <= $request->max_price;
                }

                if (!$product->variations) return false;

                $filteredVariations = collect($product->variations)->map(function ($variation) use ($request) {
                    $prices = json_decode($variation->price, true);
                    $discountPrice=json_decode($variation->discount_price, true);
                    $filteredPrices = array_values(array_filter($prices, function ($price,$index) use ($request,$discountPrice) {
                        if(isset($discountPrice[$index])){
                             $passMin = !$request->min_price || $discountPrice[$index] >= $request->min_price;
                            $passMax = !$request->max_price || $discountPrice[$index] <= $request->max_price;
                            return $passMin && $passMax;
                        }
                        $passMin = !$request->min_price || $price >= $request->min_price;
                        $passMax = !$request->max_price || $price <= $request->max_price;
                        return $passMin && $passMax;
                    }, ARRAY_FILTER_USE_BOTH));

                    if (!empty($filteredPrices)) {
                        $variation->price = json_encode($filteredPrices);
                        return $variation;
                    }

                    return null;
                })->filter()->values();

                if ($filteredVariations->isNotEmpty()) {
                    $product->variations = $filteredVariations;
                    return true;
                }

                return false;
            })->values();
            // Manual paginate
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $filteredProducts->forPage($request->page, $request->limit),
                $filteredProducts->count(),
                $request->limit,
                $request->page
            );

            if ($result->total() == 0) {
                return $this->sendError(204, 'No Product Found');
            }

             return response()->json([
                'success' => true,
                'total' => $paginated->total(),
                'can_load_more' => $paginated->currentPage() < $paginated->lastPage(),
                'data' => ProductResource::collection($paginated)
            ]);
         }
        //  $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        //     $result->forPage($request->page, $request->limit),
        //     $result->count(),
        //     $request->limit,
        //     $request->page
        // );
        //  return response()->json([
        //     'success' => true,
        //     'total' => $result->total(),
        //     'can_load_more' => $result->currentPage() < $paginated->lastPage(),
        //     'data' => ProductResource::collection($paginated)
        // ]);

         $totalPages = ceil($result->total() / $request->limit);

        if ($result->total() == 0) {
            return $this->sendError(204, 'No Product Found');
        }

        return response()->json([
            'success' => true,
            'total' => $result->total(),
            'can_load_more' => $result->total() == 0 || $request->page >= $totalPages ? false : true,
            'data' => ProductResource::collection($result)
        ], 200);


    }

    //product detail
    public function detail($id)
    {
        $product = Product::where('id', $id)->with('brand', 'category', 'image')->first();
        if (!$product) {
            return $this->sendError(204, 'No Product Found');
        }
        return $this->sendResponse('success', new ProductResource($product));
    }

    //product images
    public function productImages($id)
    {
        $images = ProductImage::where('product_id', $id)->get();
        if (!$images->count()) {
            return $this->sendError(204, 'No Product Images Found');
        }
        return $this->sendResponse('success', ProductImageResource::collection($images));
    }

    public function newArrivals(Request $request){
          $request->validate([
            'page' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);
        $products = Product::where('is_new_arrival', 1)->orderBy('id', 'desc')->active()->paginate(request('limit'));
        if (!$products->count()) {
            return $this->sendError(204, 'No Product Found');
        }

         $totalPages = ceil($products->total() / $request->limit);

        if ($products->total() == 0) {
            return $this->sendError(204, 'No Product Found');
        }

        return response()->json([
            'success' => true,
            'total' => $products->total(),
            'can_load_more' => $products->total() == 0 || $request->page >= $totalPages ? false : true,
            'data' => ProductResource::collection($products)
        ], 200);
    }

}
