<?php

namespace App\Http\Controllers\Backend;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index(){
        $suppliers=Supplier::paginate(5);
        return view('backend.suppliers.index',compact('suppliers'));
    }

    public function create(){
        return view('backend.suppliers.create');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'phone'=>'required|numeric|unique:suppliers,phone',
            'email'=>'required|email|unique:suppliers,email',
            'address'=>'required|string|max:255',
        ]);

        $validatedData=$request->all();
        Supplier::create($validatedData);
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier){
        return view('backend.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request , Supplier $supplier){
        $request->validate([
            'name'=>'required|string|max:255',
            'phone'=>'required|numeric|unique:suppliers,phone,'.$supplier->id,
            'email'=>'required|email|unique:suppliers,email,'.$supplier->id,
            'address'=>'required|string|max:255',
        ]);

        $validatedData=$request->all();
        $supplier->update($validatedData);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier){
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');

    }
}
