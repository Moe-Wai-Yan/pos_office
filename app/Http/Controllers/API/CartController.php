<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{

       public function index()
    {
        $customer = Auth::guard('api')->user();
        $cart = Cart::where('customer_id', $customer->id)->with('cartItems.product')->first();
        if ($cart) {
            return response()->json([
                'status'=>'success',
                'data' => new CartResource($cart)
            ]);
        } else {
            return response()->json([
                'status'=>'error',
                'message' => 'Cart not found'
            ],404);
        }
    }

     public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $customer = Auth::guard('api')->user();
            $cart = Cart::where('customer_id', $customer->id)->first();

            $product = Product::find($request->product_id);
            $productName = $product->name;
            if ($request->product_variation_id) {
                $product = ProductVariation::find($request->product_variation_id);
            }
            $stock = $product->stock;
            $variationStock=json_decode($stock,true);
            if ($request->option_id) {
                $optionId=$request->option_id ?? null;
                $index=array_search($optionId,json_decode($product->option_type_ids,true));
                if ($index !== false) {
                   $stock=$variationStock[$index];
                }
            }

            if (!$cart) {
                $cart = new Cart();
                $cart->customer_id = $customer->id;
                $cart->save();
            }

            $existingItem = CartItem::where([
                ['cart_id', '=', $cart->id],
                ['product_id', '=', $request->product_id]
            ])
                ->when($request->product_variation_id, function ($query, $variationId) {
                     return $query->where('product_variation_id', '=', $variationId);
                })
                ->when($request->option_id,function($query,$optionId){
                    return $query->where('option_id','=',$optionId);
                })
                ->first();

            $existingStock = $existingItem ? $existingItem->quantity : 0;
            if ($stock < ($request->quantity + $existingStock)) {
                  return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock for ' . $productName
                ],422);
            }
            if ($existingItem) {
               if ($request->product_variation_id) {
                //existing product variation
                $allowOptions = json_decode($product->option_type_ids,true);
                $userOptionId=$request->option_id;
                if (in_array($userOptionId,$allowOptions)) {
                    $existingItem->quantity +=(int) $request->quantity;
                    // $priceArray = json_decode($product->price,true);
                    // $price = intval($priceArray[$index]);
                    $existingItem->option_id=$request->option_id;
                    // dd($price);
                    $existingItem->price = (int)$request->price;
                    $existingItem->sub_total = $existingItem->quantity * (int)$request->price;
                    $existingItem->save();
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Option not found'
                    ],404);
                }

               }else{
                //single for existing product
                 $existingItem->quantity +=(int) $request->quantity;
                //  if ($existingItem->option_id) {
                //     # code...
                //  }
                // $priceArray = json_decode($product->price,true);
                // $optionArray = json_decode($product->option_type_ids,true);
                // $index=array_search($request->option_id,$optionArray);
                // $price = intval($priceArray[$index]);
                $existingItem->option_id=$request->option_id;
                // dd($price);
                $existingItem->price = (int)$request->price;
                $existingItem->sub_total = $existingItem->quantity * (int)$request->price;
                $existingItem->save();
               }
            } else {
               if ($request->product_variation_id) {
                //variation for existing product
                $allowOptions=json_decode($product->option_type_ids,true);
                $userOptionId=$request->option_id;
                if (in_array($userOptionId,$allowOptions)) {
                     $cartItem = new CartItem();
                    $cartItem->cart_id = $cart->id;
                    $cartItem->product_id = $request->product_id;
                    $cartItem->product_variation_id = $request->product_variation_id;
                    $quantity=(int)$request->quantity;
                    $cartItem->option_id=$request->option_id;
                     $priceArray = json_decode($product->price,true);
                    // dd($priceArray, gettype($priceArray));
                    $price = intval($priceArray[0]);
                    $cartItem->quantity = $quantity;
                    $cartItem->price = $request->price;
                    $cartItem->sub_total = $quantity * $request->price;
                    $cartItem->save();
                }else{
                    return response()->json([
                        'status'=>'error',
                        'message' => 'option not found'
                    ],404);
                }
               }else{
                 $cartItem = new CartItem();
                $cartItem->cart_id = $cart->id;
                $cartItem->product_id = $request->product_id;
                $cartItem->product_variation_id = $request->product_variation_id;
                $quantity=(int)$request->quantity;
                $cartItem->option_id=$request->option_id;
                //  $priceArray = json_decode($product->price,true);
                // dd($priceArray, gettype($priceArray));
                // $price = intval($priceArray[0]);
                $cartItem->quantity = $quantity;
                $cartItem->price = $request->price;
                $cartItem->sub_total = $quantity * $request->price;
                $cartItem->save();
               }
            }

            DB::commit();
            return response()->json([
               'cart'=> new CartResource($cart),
               'success' => true,
               'message'=>'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // return $this->sendError(500, $e->getMessage());

            return response()->json([
                 'success' => false,
                'message' => $e->getMessage(),

            ]);
        }
    }

    public function update(Request $request)
    {
        $cartItem = CartItem::find($request->cart_item_id);
        if ($cartItem) {
            $product = Product::find($cartItem->product_id);
            $productName = $product->name;
            if ($cartItem->product_variation_id) {
                $product = ProductVariation::find($cartItem->product_variation_id);

            }
            $option=json_decode($product->option_type_ids,true);
            $variationStock=json_decode($product->stock,true);
            $index=in_array($cartItem->option_id,$option);
            $optionSelect=array_search($cartItem->option_id,$option);
            $variationStock=$variationStock[$optionSelect];

            if ($index) {
                 if ($variationStock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock for ' . $productName

                    ],422);
                 }
            }
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock for ' . $productName

                ],422);
            }
            $cartItem->quantity = (int)$request->quantity;
            $cartItem->price =(int) $cartItem->price;
            $cartItem->sub_total = (int)$request->quantity *(int)$cartItem->price;

            $cartItem->save();
             return response()->json([
               'cart'=> new CartResource($cartItem->cart),
               'success' => true,
               'message'=>'success'
            ]);
        } else {
             return response()->json([
                 'success' => false,
                'message' => 'cart item not found',

            ],404);
        }
    }

     public function remove(Request $request)
    {
        $cartItem = CartItem::find($request->cart_item_id);
        if ($cartItem) {
            $cartItem->delete();
             return response()->json([
               'cart'=> new CartResource($cartItem->cart),
               'success' => true,
               'message'=>'success'
            ]);
        } else {
             return response()->json([
                 'success' => false,
                'message' =>'cart item not found',

            ],404);
        }
    }

    public function clear()
    {
        $customer = Auth::guard('api')->user();
        $cart = Cart::where('customer_id', $customer->id)->with('cartItems.product')->first();
        if ($cart) {
            $cart->cartItems()->delete();
            $cart->delete();

            return response()->json([
               'success' => 'success',
               'message'=>'cart cleared'
            ]);
        } else {
            return response()->json([
                 'success' => false,
                'message' =>'cart item not found',

            ],404);
        }
    }
}
