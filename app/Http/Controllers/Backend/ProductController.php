<?php

namespace App\Http\Controllers\Backend;

use Exception;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductVariation;
use App\Models\Variation;
use App\Models\VariationType;
use App\Models\VolumePricing;

class ProductController extends Controller
{
    // public $productImageArray = [];
    /**
     * product listing view
     *
     * @return void
     */
    public function listing()
    {
        return view('backend.products.index');
    }

    /**
     * Product create
     *
     * @return void
     */
    public function create()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::orderBy('id', 'desc')->get();
        $variations = Variation::orderBy('id', 'desc')->get();
        $types = VariationType::orderBy('id', 'desc')->get();
        // $subCategories = Category::where('parent_id', null)->orderBy('id', 'desc')->get();
        return view('backend.products.create', compact('categories', 'brands', 'variations', 'types'));
    }

    /**
     * Product Store
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreProductRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->category_id = $request->category_id ?? null;
            $product->brand_id = $request->brand_id ?? null;
            $product->sub_category_id = $request->sub_category_id ?? null;
            $product->discount_price=$request->discount_price ?? null;
            $product->description = $request->description ?? null;
            // $product->english_colors = $request->english_colors ?? [];

            // if($request->english_colors){
            //     foreach($request->english_colors as $en_color){
            //         $myanmar_colors[] = ProductColor::select('myanmar_name')->where('english_name',$en_color)->get()->value('myanmar_name');
            //     }
            // }
            // $product->myanmar_colors = $myanmar_colors ?? [];
            // $product->sizes = $request->sizes ?? [];
             if($request->is_new_arrival) {
                $product->is_new_arrival = 1;
            } else {
                $product->is_new_arrival = 0;
            }
            $product->product_type = $request->product_type;
            $product->save();

            // dd($request->all());
            $option_ids = [];
            $stocks = [];
            $prices = [];
            if ($request->product_type == "1") {
                 $product->price = $request->price;
                $product->stock = $request->stock;
                $product->update();
            } else {
                $option_ids = [];
                $prices = [];
                $stocks = [];
                $discounts=[];

                 if ($request->prices) {
                    foreach ($request->prices as $uniqueKey => $price) {
                        $option_ids[$uniqueKey] = $request->option_variation_ids[$uniqueKey] ?? null;
                        $stocks[$uniqueKey] = $request->stocks[$uniqueKey] ?? null;
                        $prices[$uniqueKey] = $price;
                        $discounts[$uniqueKey] = $request->discounts[$uniqueKey] ?? null;
                    }
                }

                foreach ($request->colors as $uniqueKey => $colorArray) {
                    // dd($uniqueKey,$colorArray);
                    $variation = new ProductVariation();
                    $variation->product_id = $product->id;
                    $variation->variation_id = $request->variations[$uniqueKey] ?? null;
                    $variation->variation_type_id = $request->types[$uniqueKey] ?? null;
                    $variation->color = $colorArray[0] ?? null;
                    $variation->option_type_ids = isset($option_ids[$uniqueKey]) ? json_encode($option_ids[$uniqueKey]) : null;
                    $variation->price = isset($prices[$uniqueKey]) ? json_encode($prices[$uniqueKey]) : null;
                    $variation->stock = isset($stocks[$uniqueKey]) ? json_encode($stocks[$uniqueKey]) : null;
                    $variation->discount_price=isset($discounts[$uniqueKey]) ? json_encode($discounts[$uniqueKey]) : null;
                    $variation->save();
                }
            }

            if ($request->hasFile('images')) {
                $this->_createProductImages($product->id, $request->file('images'));
            }

            DB::commit();
            return redirect()->route('product')->with('created', 'Product Created Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Product detail
     *
     */
    public function detail(Product $product)
    {
        $product->with('brand', 'category', 'images', 'variations')->first();
        $data = [
            'product' => $product,
        ];

        return view('backend.products.detail')->with($data);
    }

    /**
     * Create Review Images
     */
    private function _createProductImages($productId, $files)
    {
        $productImageArray = [];
        foreach ($files as $image) {
            $productImageArray[] = [
                'product_id'      => $productId,
                'path'           => $image->store('products'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        ProductImage::insert($productImageArray);
    }

    /**
     * Product edit
     *
     * @param StoreProductRequest $request
     * @param [type] $id
     * @return void
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::orderBy('id', 'desc')->get();
        $variations = Variation::orderBy('id', 'desc')->get();
        $types = VariationType::orderBy('id', 'desc')->get();
         $subCategories = $product->category->subCategories ?? collect([]);
        return view('backend.products.edit', compact('product', 'categories', 'brands', 'variations','subCategories', 'types'));
    }

    /**
     * Update Product
     *
     * @param [type] $id
     * @param StoreProductRequest $request
     * @return void
     */
    public function update(Product $product, UpdateProductRequest $request)
    {
        // dd($request->all());
        if (empty($request->old) && empty($request->images)) {
            return redirect()->back()->with('fail', 'Product Image is required');
        }

        DB::beginTransaction();
        try {
            $product->name = $request->name;
            // $product->price = $request->price;

            $product->category_id = $request->category_id ?? null;
             $product->sub_category_id = $request->sub_category_id ?? null;
            $product->brand_id = $request->brand_id ?? null;
            $product->discount_price=$request->discount_price ?? null;
            $product->description = $request->description;
            if ($request->product_type == 1) {
                $product->price = $request->price;
                $product->stock = $request->stock;
            } else {
                $product->price = null;
                $product->stock = 0;
            }
            $product->product_type = $request->product_type;
             if($request->is_new_arrival) {
                $product->is_new_arrival = 1;
            } else {
                $product->is_new_arrival = 0;
            }
            $product->update();

            // old image file delete
            if ($request->has('old')) {
                $files = $product->images()->whereNotIn('id', $request->old)->get();## oldimg where not in request old
                if (count($files) > 0) { ## delete oldimg where not in request old
                    foreach ($files as $file) {
                        $oldPath = $file->getRawOriginal('path') ?? '';
                        Storage::delete($oldPath);
                    }

                $oldImageIds = $request->input('old', []);

                if (!is_array($oldImageIds)) {
                    $oldImageIds = [];
                }

                $product->images()
                    ->whereNotIn('id', $oldImageIds)
                    ->delete();

                //   $productImages=$product->images()->whereNotIn('id', $request->old);
                // //   dd($productImages);
                //  $productImages->delete();
                    // $product->images()->whereNotIn('id', $request->old)->delete();
                }
            }

            if ($request->hasFile('images')) {
                $this->_createProductImages($product->id, $request->file('images'));
            }

            $option_ids = [];
            $stocks = [];
            $prices = [];
            $discounts=[];
            if ($request->product_type == 2) {

                if ($request->prices) {
                     foreach ($request->prices as $key => $price) {
                                if ($request->option_variation_ids) {
                                    $option_ids[] = $request->option_variation_ids[$key];
                                }
                                $stocks[] = $request->stocks[$key];
                                $prices[] = $price;
                            }
                    foreach ($request->variations as $key => $var_id) {
                        $variationId = $request->variation_ids[$key] ?? null;

                        $variation = ProductVariation::find($variationId) ?? new ProductVariation();

                        $variation->product_id = $product->id;
                        $variation->variation_id = $var_id;
                        $variation->variation_type_id = $request->types[$key] ?? null;


                        // $priceData = is_string($request->prices[$key]) ? json_decode($request->prices[$key], true) : $request->prices[$key];
                        // $stockData = is_string($request->stocks[$key]) ? json_decode($request->stocks[$key], true) : $request->stocks[$key];

                        // $variation->price = json_encode($priceData);
                        // $variation->stock = json_encode($stockData);


                        // ✅ Get discounts by variation ID (like 21)
                        $variation->discount_price = json_encode($request->discounts[$variationId] ?? []);

                        $variation->save();

                         if (!$variation) {
                            $variation = new ProductVariation();
                        }
                        $variation->product_id = $product->id;
                        $variation->variation_id = $var_id ?? null;
                        $variation->variation_type_id = $request->types[$key] ?? null;
                        $colorArray = array_values($request->colors ?? []);
                        $variation->color = $colorArray[$key] ?? null;
                        $variation->option_type_ids = count($option_ids) > 0 ? json_encode($option_ids[$key] ?? '') : null;
                        $variation->price = json_encode($prices[$key]);
                        $variation->stock = json_encode($stocks[$key]);
                        $variation->save();
                    }

                }


            } else {
                ProductVariation::where('product_id', $product->id)->delete();
            }

            // if ($request->product_type == 2) {
            //     if ($request->prices) {
            //         foreach ($request->prices as $key => $price) {
            //             if ($request->option_variation_ids) {
            //                 $option_ids[] = $request->option_variation_ids[$key];
            //             }
            //             $stocks[] = $request->stocks[$key];
            //             $prices[] = $price;
            //             $discounts[]=$request->discounts[$key] ?? null;
            //         }
            //     }
            //     if ($request->variation_ids) {
            //         ProductVariation::where('product_id', $product->id)->whereNotIn('id', $request->variation_ids)->delete();
            //     }
            //     foreach ($request->variations  as $key => $var_id) {
            //         $variation = null;
            //         if ($request->variation_ids && array_key_exists($key, $request->variation_ids)) {
            //             $variation = ProductVariation::find($request->variation_ids[$key]);
            //         }
            //         if (!$variation) {
            //             $variation = new ProductVariation();
            //         }
            //         $variation->product_id = $product->id;
            //         $variation->variation_id = $var_id ?? null;
            //         $variation->variation_type_id = $request->types[$key] ?? null;
            //         $colorArray = array_values($request->colors ?? []);
            //         $variation->color = $colorArray[$key] ?? null;
            //         $variation->option_type_ids = count($option_ids) > 0 ? json_encode($option_ids[$key] ?? '') : null;
            //         $variation->price = json_encode($prices[$key]);
            //         $variation->stock = json_encode($stocks[$key]);
            //         $variation->discount_price=json_encode($discounts[$key]);
            //         $variation->save();
            //     }
            // } else {
            //     ProductVariation::where('product_id', $product->id)->delete();
            // }

            DB::commit();
            return redirect()->route('product')->with('updated', 'Product Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Product destroy
     *
     * @param [type] $id
     * @return void
     */
    public function destroy(Product $product)
    {
        $wholesales = VolumePricing::where('product_id', $product->id)->get();
        foreach ($wholesales as $wholesale) {
            $wholesale->delete();
        }

        $product->update(['status' => '0']);
        return 'success';
    }

    /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $product = Product::with('brand', 'category', 'image')->active()->orderBy('id', 'desc')->get();
        return datatables($product)
            ->addColumn('image', function ($each) {
                $image = $each->image;
                return '<img src="' . $image->path . '" class="thumbnail_img"/>';
            })
            ->addColumn('category', function ($each) {
                return $each->category->name ?? '---';
            })
            ->addColumn('brand', function ($each) {
                return $each->brand->name ?? '---';
            })
            ->editColumn('price', function ($each) {
                return number_format($each->price,) . ' MMK';
            })
            ->editColumn('instock', function ($each) {
                if ($each->instock == 1) {
                    $instock = '<div class="badge badge-soft-success">instock</div>';
                } else {
                    $instock = '<div class="badge badge-soft-danger">out of stock</div>';
                }
                return $instock;
            })
            ->addColumn('action', function ($each) {

                $show_icon = '<a href="' . route('product.detail', $each->id) . '" class="detail_btn btn btn-sm btn-info"><i class="ri-eye-fill btn_icon_size"></i></a>';
                $edit_icon = '<a href="' . route('product.edit', $each->id) . '" class="btn btn-sm btn-success edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
                $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="' . $each->id . '"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';
                return '<div class="action_icon d-flex gap-3">' . $show_icon . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['category', 'instock', 'brand', 'action', 'image'])
            ->toJson();
    }

    /**
     * Product images
     *
     * @return void
     */
    public function images(Product $product)
    {
        $oldImages = [];
        foreach ($product->images as $img) {
            $oldImages[] = [
                'id'  => $img->id,
                'src' => $img->path,
            ];
        }

        return response()->json($oldImages);
    }

    public function fetchVariations($id)
    {
        $productVariations = ProductVariation::where('product_id', $id)->get();
        foreach ($productVariations as $productVariation) {
            $variation = Variation::find($productVariation->variation_id);
            $type = VariationType::find($productVariation->variation_type_id);
            $productVariation->variation_name = $variation->name ?? '---';
            $productVariation->type_name = $type->name ?? '---';
        }
        return response()->json([
            'variations' => $productVariations
        ]);
    }

    public function fetchVariationOptions($id)
    {
        $productVariation = ProductVariation::find($id);
        $options = VariationType::whereIn('id', json_decode($productVariation->option_type_ids))->get()->toArray();
        return response()->json([
            'options' => $options
        ]);
    }
}
