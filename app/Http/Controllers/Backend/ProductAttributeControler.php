<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductAttributeControler extends Controller
{
    public function index(){
        $productAttributes = \App\Models\ProductAttribute::paginate(5);
        return view('backend.product_attributes.index',compact('productAttributes'));
    }

    public function create(){
        return view('backend.product_attributes.create');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
        ]);

        $validatedData=$request->all();
        \App\Models\ProductAttribute::create($validatedData);
        return redirect()->route('product-attributes.index')->with('success', 'Product attribute created successfully.');
    }

    public function edit(\App\Models\ProductAttribute $productAttribute){
        return view('backend.product_attributes.edit', compact('productAttribute'));
    }

    public function update(Request $request , \App\Models\ProductAttribute $productAttribute){
        $request->validate([
            'name'=>'required|string|max:255',
        ]);

        $validatedData=$request->all();
        $productAttribute->update($validatedData);
        return redirect()->route('product-attributes.index')->with('success', 'Product attribute updated successfully.');
    }

    public function destroy(\App\Models\ProductAttribute $productAttribute){
        $productAttribute->delete();
        return redirect()->route('product-attributes.index')->with('success', 'Product attribute deleted successfully.');
    }
}
