<?php

namespace App\Http\Controllers\API;

use App\Events\NewOrderEvent;
use Exception;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\OrderSuccessMessage;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\StoreOrderRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\VolumePricing;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends BaseController
{
    //list
    public function list()
    {
        $orders = Order::getRelationData()->orderBy('id', 'DESC')->get();
        if (!count($orders)) {
            return $this->sendError(204, 'No Data Found');
        }
        return $this->sendResponse('success', OrderResource::collection($orders));
    }

    //detail
    public function detail($id)
    {
        $order = Order::getRelationData()->where('id', $id)->get();
        if (!$order->count()) {
            return $this->sendError(204, 'No Order Found');
        }
        return $this->sendResponse('success', new OrderResource($order[0]));
    }

    //create order
    public function create(StoreOrderRequest $request)
    {
        $orderData = $this->getRequestOrderData($request);

        DB::beginTransaction();
        try {

            $order = Order::create($orderData);

            $orderItemsData = $this->getRequestOrderItemsData($request, $order->id);
            OrderItem::insert($orderItemsData);

            event(new NewOrderEvent($this->getNotificationData($order->id)));

            $message = $this->getOrderSuccessfulMessage();
            DB::commit();

            return $this->sendResponse($message, $order);
        } catch (Exception $e) {

            DB::rollBack();
            throw $e;
            return $this->sendError(500, 'Something wrong!Please Try Again.');
        }
    }

    // get new order notification data for admin
    private function getNotificationData($orderId)
    {
        $data = Order::with(['customer' => function ($query) {
            $query->select('id', 'name');
        }])->where('id', $orderId)->first();

        $data->message = 'placed a new order';

        return $data;
    }

    //get order successful message
    private function getOrderSuccessfulMessage()
    {
        $orderSuccessMessage = OrderSuccessMessage::first();
        if (!$orderSuccessMessage) {
            return 'Order တင်ခြင်း အောင်မြင်ပါသည်။';
        }
        return $orderSuccessMessage->value('message');
    }

    //get order data
    private function getRequestOrderData($request)
    {
        $data = [
            'customer_id' => Auth::guard('api')->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'delivery_fee' => $request->delivery_fee,
            'region_id' => $request->region_id,
            'delivery_fee_id' => $request->delivery_fee_id,
        ];

        if ($request->payment_method == 'payment') {
            $data['payment_id'] = $request->payment_id;
            if ($request->hasFile('payment_photo')) {
                $image = $request->file('payment_photo');
                $data['payment_photo'] = $image->store('payment-photos');
            }
        }

        $subTotal = 0;
        $discountTotal = 0;

        foreach (json_decode($request->carts) as $cart) {
            $volumePrices = VolumePricing::where('product_id', $cart->product_id)
                ->where('quantity', $cart->quantity)
                ->when($cart->product_variation_id, function ($q) use ($cart) {
                    return $q->where('product_variation_id', $cart->product_variation_id);
                })
                ->first();

            if ($volumePrices) {
                $totalPrice = $cart->quantity * $volumePrices->discount_price;
                $discountTotal += ($cart->quantity * $cart->price) - $totalPrice;
            } else {
                $totalPrice = $cart->quantity * $cart->price;
            }

            $subTotal += $totalPrice;
        }

        $data['sub_total'] = $subTotal;
        $data['discount_amount'] = $discountTotal;
        $data['grand_total'] = $request->delivery_fee + $subTotal;

        return $data;
    }

    //get order items data
    private function getRequestOrderItemsData($request, $orderId)
    {
        $carts = json_decode($request->carts);

        foreach ($carts as $cart) {
            $orderItem = [
                'order_id' => $orderId,
                'product_id' => $cart->product_id,
                'product_variation_id' => $cart->product_variation_id,
                'option_id' => $cart->option_id,
                'price' => $cart->price,
                'quantity' => $cart->quantity,
                'created_at' => now(),
                'updated_at' => now()
                // 'total_price' => $cart->total_price,
            ];
            $volumePrices = VolumePricing::where('product_id', $cart->product_id)
                ->where('quantity', $cart->quantity)
                ->when($cart->product_variation_id, function ($q) use ($cart) {
                    return $q->where('product_variation_id', $cart->product_variation_id);
                })
                ->first();
            if ($volumePrices) {
                $orderItem['total_price'] = $cart->quantity * $volumePrices->discount_price;
            } else {
                $orderItem['total_price'] = $cart->quantity * $cart->price;
            }

            $product = Product::find($cart->product_id);
            if ($cart->product_variation_id) {
                $product = ProductVariation::find($cart->product_variation_id);
            }
            if ($cart->option_id) {
                $index = array_search($cart->option_id, json_decode($product->option_type_ids));
                if ($index !== false) {
                    $stocks = json_decode($product->stock);
                    if ($stocks[$index] >= $cart->quantity) {
                        $stocks[$index] -= $cart->quantity;
                        $product->stock = json_encode($stocks);
                        $product->save();
                    }
                }
            } else {
                if($product->variation_id) {

                    $stocks = json_decode($product->stock);
                    if ($stocks[0] >= $cart->quantity) {
                        $stocks[0] -= $cart->quantity;
                        $product->stock = json_encode($stocks);
                        $product->save();
                    }
                } else {
                    if($product->stock >= $cart->quantity) {
                        $product->stock -= $cart->quantity;
                        $product->save();
                    }
                }
            }
            $orderItems[] = $orderItem;
        }

        return $orderItems;
    }

    // private function getRequestOrderItemsData($request, $orderId)
    // {
    //     $cart = Cart::where('customer_id', Auth::guard('api')->user()->id)->first();
    //     $cartItems = CartItem::where('cart_id', $cart->id)->get();

    //     foreach ($cartItems as $item) {
    //         $product = null;
    //         if (!$item->product_variation_id) {
    //             $product = Product::find($item->product_id);
    //         } else {
    //             $product = ProductVariation::find($item->product_variation_id);
    //         }
    //         if ($product->stock < $item->quantity) {
    //             throw ValidationException::withMessages([
    //                 'stock' => "Not enough stock for product ID {$item->product_id}"
    //             ]);
    //         }
    //         $orderItem = [
    //             'order_id' => $orderId,
    //             'product_id' => $item->product_id,
    //             'product_variation_id' => $item->product_variation_id,
    //             'price' => $product->price,
    //             // 'wholesale_price' => $product->wholesale_price,
    //             'quantity' => $item->quantity,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //             // 'total_price' => $item->total_price,
    //         ];
    //         $orderItem['total_price'] = $item->quantity * $product->price;


    //         if ($product->stock >= $item->quantity) {
    //             $product->stock -= $item->quantity;
    //             $product->save();
    //         }
    //         $orderItems[] = $orderItem;
    //     }

    //     return $orderItems;
    // }
}
