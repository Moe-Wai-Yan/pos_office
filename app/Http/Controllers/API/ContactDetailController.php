<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactDetail;
use Illuminate\Http\Request;

class ContactDetailController extends BaseController
{
    public function index() {
        $detail = ContactDetail::first();
        return $this->sendResponse("Contact Detail", $detail);
    }
}
