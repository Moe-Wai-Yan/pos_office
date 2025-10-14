<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    public function list()
    {
        $noti = Notification::where('notifiable_type', 'App\Models\Customer')->where('notifiable_id', Auth::user()->id)->latest()->get();
        return $this->sendResponse("Notification list!", NotificationResource::collection($noti));
    }

    public function read($id)
    {
        $noti = Notification::find($id);
        if ($noti) {
            $noti->read_at = now();
            $noti->update();
            return $this->sendResponse("Notification read!", new NotificationResource($noti));
        } else {
            return $this->sendError(422, "Notification not found!");
        }
    }
}
