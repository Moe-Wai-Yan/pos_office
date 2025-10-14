<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
      public function index()
    {
        $warehouses = Warehouse::get();
        return view('backend.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:warehouses,phone',
            'email'=>'required|unique:warehouses,email',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);
        $validatedData=$request->all();
         Warehouse::create($validatedData);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:warehouses,phone,'.$warehouse->id,
            'email'=>'required|unique:warehouses,email,'.$warehouse->id,
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);
        if (!$request->is_active) {
            $warehouse->update(['is_active' => 0]);
        }
        $validatedData=$request->all();
        $warehouse->update($validatedData);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return back()->with('success', 'Warehouse deleted successfully.');
    }
}
