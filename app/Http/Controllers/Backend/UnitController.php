<?php

namespace App\Http\Controllers\Backend;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function index(){
        $units=Unit::paginate();
        return view('backend.units.index',compact('units'));
    }

    public function create(){
        return view('backend.units.create');
    }

    public function store(Request $request){

        $request->validate([
            'code'=>'required|string|max:255',
            'name'=>'required|string|max:255',
            'base_ratio'=>'required|numeric',
        ]);

        $validatedData=$request->all();

       $unit= Unit::create($validatedData);
        return redirect()->route('units.index')->with('success', 'Unit created successfully.');

    }

    public function edit(Unit $unit){
        return view('backend.units.edit',compact('unit'));
    }

    public function update(Request $request , Unit $unit){
        $request->validate([
            'code'=>'required|string|max:255',
            'name'=>'required|string|max:255',
            'base_ratio'=>'required|numeric',
        ]);

        $validatedData=$request->all();
        $unit->update($validatedData);
        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit){
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
