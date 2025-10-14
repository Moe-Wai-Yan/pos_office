<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\OrderCancelRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\FcmTokenKey;
use App\Models\Notification;
use Illuminate\Support\Str;
use Google_Client;

class OrderController extends Controller
{
    //index
    public function index()
    {
        return view('backend.orders.index');
    }


    public function refundOrderList()
    {
        return view('backend.orders.refund-order');
    }

    public function getRefundList()
    {
        return $this->serverSide(null, true);
    }

    public function orderByStatus()
    {
        $orderStatus = [
            'pending',
            'confirm',
            'processing',
            'delivered',
            'complete',
            'cancel'
        ];
        if (!in_array(request()->status, $orderStatus)) {
            return abort(404);
        }
        return view('backend.orders.pending-order')->with(['status' => request()->status]);
    }

    //detail
    public function detail(Order $order, $notiId = null)
    {
        if ($notiId) {
            if ($notiId) {
                auth()->user()->notifications->where('id', $notiId)->markAsRead();
            }
        }
        $orderDetail = Order::with(
            'orderItem',
            'orderItem.product',
            'orderItem.product.image',
            'orderItem.productVariation',
            'orderItem.productVariation.variation',
            'orderItem.productVariation.type',
            'orderItem.optionVariation',
            'payment',
            'customer',
            'deliveryFeeRelation',
            'deliveryFeeRelation.region'
        )->where('id', $order->id)->first()->toArray();
        // dd($orderDetail);
        return view('backend.orders.detail')->with(['order' => $orderDetail]);
    }

    //update order status
    public function updateStatus(Order $order, UpdateOrderRequest $request)
    {
        $order->update([
            'status' => $request->status,
        ]);
        $this->saveNoti($request->status, $order->customer_id);
        $this->sendPushNotification($request->status, $order->customer_id);

        return response()->json([
            'message' => 'Order updated successfully',
        ]);
    }

    public function cancelOrder(Order $order)
    {
        return view('backend.orders.cancel')->with(['order' => $order]);
    }

    public function refundOrder(Order $order)
    {
        return view('backend.orders.refund')->with(['order' => $order]);
    }


    public function saveCancelOrder(Order $order, OrderCancelRequest $request)
    {
        $order = Order::with('orderItem.productVariation')->find($order->id);

        if (!empty($order->orderItem)) {
            foreach ($order->orderItem as $item) {
                if ($item->productVariation) {
                    // dd($item->productVariation);
                    $optionIds=json_decode($item->productVariation->option_type_ids,true);
                    $oldStock= json_decode($item->productVariation->stock,true);
                   $index=array_search($item->option_id,$optionIds);
                    if ($index !== false) {
                      $oldStock[$index]=$oldStock[$index]+(int)$item->quantity;
                      $item->productVariation->stock=json_encode($oldStock);
                      $item->productVariation->save();
                    }
                }
                if($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }


        $order->update(['cancel_message' => $request->message, 'status' => 'cancel']);

        $title = 'Order Cancel';
        $message = 'Your order has been cancel by ' . config('app.companyInfo.name');

        //sendPushNotification($title, $message, $order->customer_id);
         $this->saveNoti('cancel', $order->customer_id);
        $this->sendPushNotification('cancel', $order->customer_id);

        return redirect()->route('order')->with(['updated', 'Order cancel လုပ်ခြင်း အောင်မြင်ပါသည်']);
    }

    public function saveRefundOrder(Order $order, Request $request)
    {
        $order->update([
            'refund_date' => Carbon::now(),
            'refund_message' => $request->message
        ]);

        $this->saveNoti('refund', $order->customer_id);
        $this->sendPushNotification('refund', $order->customer_id);

        return redirect()->route('order')->with(['updated', 'Order refund လုပ်ခြင်း အောင်မြင်ပါသည်']);
    }

    public function saveNoti($status, $customer_id)
    {
        $noti = new Notification();
        $noti->id = (string)Str::uuid();
        $noti->type = 'App\Notifications\NewOrderNotification';
        $noti->notifiable_type = 'App\Models\Customer';
        $noti->notifiable_id = $customer_id;
        $noti->data = "Your order has been $status" . "ed!";
        $noti->save();
    }

    //all order datatable
    public function getAllOrder()
    {
        return $this->serverSide();
    }

    public function getOrderByStatus($status)
    {
        return $this->serverSide($status);
    }

    //data table
    public function serverSide($status = null, $refund = false)
    {
        $order = Order::query();
        if (isset($status)) {
            $order->where('status', $status)->orderBy('id', 'desc')->get();
        } elseif ($refund) {
            $order->where('refund_date', '!=', null)->orderBy('id', 'desc')->get();
        } else {
            $order->orderBy('id', 'desc')->get();
        }
        return datatables($order)
            ->editColumn('created_at', function ($each) {
                return $each->created_at->diffForHumans() ?? '-';
            })
            ->editColumn('status', function ($each) {
                if ($each->status == "pending") {
                    $status = 'bg-danger';
                } elseif ($each->status == "finish") {
                    $status = 'bg-success';
                } elseif ($each->status == "cancel") {
                    $status = 'bg-dark';
                } else {
                    $status = 'bg-info';
                }
                $status = '<div class="badge ' . $status . '">' . $each->status . '</div>';
                $refund = '<div class="badge bg-primary my-2">refunded</div>';
                if ($each->refund_date) {
                    return '<div class="d-flex flex-column justify-content-center align-items-center">' . $status . $refund . '</div>';
                }
                return '<div class="">' . $status . '</div>';
            })
            ->addColumn('action', function ($each) {
                $show_icon = '<a href="' . route('order.detail', $each->id) . '" class="show_btn btn btn-sm btn-info mr-3"><i class="ri-eye-fill btn_icon_size"></i></a>';
                $cancel_btn = '<a href="' . route('order.cancel', $each->id) . '" class="btn btn-dark cancelBtn">Cancel</a>';
                $refund_btn = '<a href="' . route('order.refund', $each->id) . '" class="btn btn-primary " data-id="' . $each->id . '">Refund</a>';
                if ($each->refund_date) {
                    return '<div class="action_icon d-flex align-items-center">' . $show_icon . '</div>';
                }
                if ($each->status == 'cancel') {
                    return '<div class="action_icon d-flex align-items-center">' . $show_icon . $refund_btn . '</div>';
                }
                return '<div class="action_icon d-flex align-items-center">' . $show_icon . $cancel_btn .'</div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    private function sendPushNotification($status, $customerId)
    {
        $credentialsFilePath = $_SERVER['DOCUMENT_ROOT'] . '/firebase/fcm-server-key.json';
        $client = new Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        $url = 'https://fcm.googleapis.com/v1/projects/outdoor-camping-78cc5/messages:send';
        $fcm_user_key = FcmTokenKey::select('id', 'fcm_token_key')->where('customer_id', $customerId)->orderBy('id', 'desc')->get();
        foreach ($fcm_user_key as $userToken) {
            $notifications = [
                'title' => 'Order ' . $status,
                'body' => 'Your order has been ' . $status  . " by " . config('app.companyInfo.name'),
            ];
            $data = [

                'token' => $userToken->fcm_token_key,
                'notification' => $notifications,
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ]
                    ],
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ]
                ],
            ];
           $response= Http::withHeaders([
                'Authorization' => "Bearer $access_token",
                'Content-Type' => "application/json"
            ])->post($url, [
                'message' => $data
            ]);

        }


        return true;
    }
}
