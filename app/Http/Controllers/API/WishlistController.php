<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends BaseController
{
    public function list()
    {
        $products = [];
        $wishlists = Wishlist::where('customer_id', Auth::id())->get();
        foreach ($wishlists as $wishlist) {
            $product = Product::where('id', $wishlist->product_id)->where('status', 1)->first();
            if ($product) {
                $products[] = new ProductResource($product);
            }
        }
        if (!empty($products)) {
            return $this->sendResponse("Wishlist list!", ProductResource::collection($products));
        } else {
            return $this->sendError(404, "There's no wishlist product!");
        }
    }

    public function change(Request $request)
    {
        // $request->validate([
        //     'product_id' => 'required|exists:products,id'
        // ]);
        $wishlist = Wishlist::where('product_id', $request->product_id)->where('customer_id', Auth::id())->first();
        if (!$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->customer_id = Auth::id();
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
        } else {
            $wishlist->delete();
        }

        return $this->sendResponse("Wishlist changed!", $wishlist);
    }
}
