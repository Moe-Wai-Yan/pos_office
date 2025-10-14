<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhotoUpload;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Storage;

class CustomerController extends BaseController
{
    //index
    public function index()
    {
        $customer = Auth::guard('api')->user();
        return $this->sendResponse('success', new CustomerResource($customer));
    }

    //update
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required_without:phone|email|unique:customers,email,' . Auth::guard('api')->user()->id,
            'phone' => 'required_without:email|unique:customers,phone,' . Auth::guard('api')->user()->id . '|min:6|max:20',
        ]);

        $customer = Auth::guard('api')->user();
        $imagePath = $customer->image; // fallback to old image path

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = PhotoUpload::storeImage($customer, $file); // replaces $imagePath
        }

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'image' => $imagePath, // always has value (either old or new)
        ]);

        return $this->sendResponse('Your account has been updated successfully!', new CustomerResource($customer->refresh()));
    }


    //updatePassword
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $customer = Auth::guard('api')->user();
        if (!empty($customer)) {
            if (Hash::check($request->old_password, $customer->password)) {
                $customer->update([
                    'password' => Hash::make($request->password)
                ]);
                return $this->sendResponse('Your password has been updated,successfull!');
            } else {
                return $this->sendError(422, 'Your old password does not match!');
            }
        }
        return $this->sendError(404, 'There is no such data!');
    }

    public function delete(){
         $customer = Auth::guard('api')->user();
        $customer->delete();

        return $this->sendResponse('Your Account has been deleted,successfull!');
    }
}
