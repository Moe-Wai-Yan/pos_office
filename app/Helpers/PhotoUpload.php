<?php

namespace App\Helpers;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoUpload
{

    public static function storeImage($customer, $file)
    {

        $name = uniqid() . $file->getClientOriginalName();
        $path = 'public/customer-photo/';

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        Storage::putFileAs($path, $file, $name);

        if ($customer->image != null) {
            Storage::delete('public/customer-photo/' . $customer->getRawOriginal('image'));
        }

        return $name;
    }
}
