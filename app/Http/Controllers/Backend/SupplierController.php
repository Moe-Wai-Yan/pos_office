<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(){
        return view('backend.supplier.index');
    }

    public function create(){
        return view('backend.supplier.create');
    }

    public function store(Request $request){
        $request->validate([
            ''
        ])
    }
}
