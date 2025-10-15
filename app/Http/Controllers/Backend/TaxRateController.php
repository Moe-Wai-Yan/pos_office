<?php

namespace App\Http\Controllers\Backend;

use App\Models\TaxRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaxRateController extends Controller
{
    public function index(){
        $taxRates=TaxRate::paginate(5);
        return view('backend.tax_rate.index',compact('taxRates'));
    }

    public function create(){
        return view('backend.tax_rate.create');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'rate_percent'=>'required|numeric',
            'is_inclusive'=>'required|boolean',
            'is_active'=>'required|boolean',
        ]);

        $validatedData=$request->all();
        TaxRate::create($validatedData);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate created successfully.');
    }

    public function edit(TaxRate $taxRate){
        return view('backend.tax_rate.edit', compact('taxRate'));
    }

    public function update(Request $request , TaxRate $taxRate){
        $request->validate([
            'name'=>'required|string|max:255',
            'rate_percent'=>'required|numeric',
            'is_inclusive'=>'required|boolean',
            'is_active'=>'required|boolean',
        ]);

        $validatedData=$request->all();
        $taxRate->update($validatedData);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate){
        $taxRate->delete();
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate deleted successfully.');
    }


}
