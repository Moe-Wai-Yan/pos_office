<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactDetailRequest;
use App\Models\ContactDetail;
use Illuminate\Http\Request;

class ContactDetailController extends Controller
{
    public function index()
    {
        return view('backend.contact-detail.index');
    }

    public function create(){
        return view('backend.contact-detail.create');
    }

    public function store(StoreContactDetailRequest $request){
        $validatedData=$request->validated();
        ContactDetail::create($validatedData);
        return redirect()->route('contactDetail')->with(['created' => 'Contact detail created successfully']);
    }

    public function edit($id)
    {
        $contactDetail = ContactDetail::find($id);
        if ($contactDetail) {
            return view('backend.contact-detail.edit')->with(['contactDetail' => $contactDetail]);
        } else {
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $contactDetail = ContactDetail::find($id);
        if ($contactDetail) {
            $contactDetail->phone = $request->phone;
            $contactDetail->email = $request->email;
            $contactDetail->address = $request->address;
            $contactDetail->facebook_url=$request->facebook_url;
            $contactDetail->messenger_url=$request->messenger_url;
            $contactDetail->viber_url=$request->viber_url;
            $contactDetail->tiktok_url=$request->tiktok_url;
            $contactDetail->save();

            return redirect()->route('contactDetail')->with(['updated' => 'Contact detail updated successfully']);
        } else {
            return redirect()->back();
        }
    }

    public function serverSide()
    {
        $data = ContactDetail::orderBy('id', 'desc')->get();
        return datatables($data)
            ->addColumn('email', function ($each) {
                return $each->email ?? '-----';
            })
            ->addColumn('phone', function ($each) {
                return $each->phone ?? '-----';
            })
            ->addColumn('address', function ($each) {
                return $each->address ?? '-----';
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('contactDetail.edit', $each->id) . '" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">' . $edit_icon . '</div>';
            })
            ->rawColumns(['email', 'phone', 'address', 'action'])
            ->toJson();
    }
}
