<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolumePriceRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Variation;
use App\Models\VariationType;
use App\Models\VolumePricing;
use Illuminate\Http\Request;

class WholesaleController extends Controller
{
    /**
     * product listing view
     *
     * @return void
     */
    public function index()
    {
        return view('backend.wholesale.index');
    }

    /**
     * Create Form
     *
     * @return void
     */
    public function create()
    {
        $products = Product::where('status', 1)->get();
        return view('backend.wholesale.create', compact('products'));
    }

    /**
     * Store Category
     *
     * @param StoreCategoryRequest $request
     * @return void
     */
    public function store(StoreVolumePriceRequest $request)
    {
        $wholesale = new VolumePricing();
        $wholesale->product_id = $request->product_id;
        $wholesale->product_variation_id = $request->product_variation_id;
        $wholesale->option_type_id = $request->option_type_id;
        $wholesale->quantity = $request->quantity;
        $wholesale->discount_price = $request->discount_price;
        $wholesale->save();

        return redirect()->route('wholesale')->with('created', 'Wholesale created Successfully');
    }

    /**
     * Product Categeory Edit
     *
     * @param [type] $id
     * @return void
     */
    public function edit($id)
    {
        $wholesale = VolumePricing::find($id);
        $products = Product::all();
        $variations = ProductVariation::where('product_id', $wholesale->product_id)->get();
        $productVariation = ProductVariation::find($wholesale->product_variation_id);
        if ($productVariation) {
            if ($productVariation->option_type_ids) {
                $variationTypes = VariationType::whereIn('id', json_decode($productVariation->option_type_ids))->get();
            } else {
                $variationTypes = VariationType::all();
            }
        } else {
            $variationTypes = [];
        }
        return view('backend.wholesale.edit', compact('wholesale', 'products', 'variations', 'variationTypes'));
    }

    /**
     * Product Category Update
     *
     * @param Reqeuest $reqeuest
     * @param [type] $id
     * @return void
     */
    public function update(StoreVolumePriceRequest $request, $id)
    {
        $wholesale = VolumePricing::find($id);
        $wholesale->product_id = $request->product_id;
        $wholesale->product_variation_id = $request->product_variation_id;
        $wholesale->option_type_id = $request->option_type_id;
        $wholesale->quantity = $request->quantity;
        $wholesale->discount_price = $request->discount_price;
        $wholesale->update();

        return redirect()->route('wholesale')->with('updated', 'Wholesale Updated Successfully');
    }


    /**
     * delete Category
     *
     * @return void
     */
    public function destroy($id)
    {
        $wholesale = VolumePricing::find($id);

        $wholesale->delete();

        return 'success';
    }

    /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $wholesale = VolumePricing::orderBy('id', 'desc')->get();
        return datatables($wholesale)
            ->addColumn('name', function ($each) {
                $productName = $each->product->name;
                if ($each->product_variation_id) {
                    $pv = ProductVariation::with(['variation', 'type'])->find($each->product_variation_id);
                    if ($pv && $pv->variation && $pv->type) {
                        $productName .= " ({$pv->variation->name} : {$pv->type->name})";
                    }
                }
                return $productName;
            })
            ->addColumn('quantity', function ($each) {
                return $each->quantity ?? '-----';
            })
            ->addColumn('discount_price', function ($each) {
                return $each->discount_price ?? '-----';
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('wholesale.edit', $each->id) . '" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
                $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="' . $each->id . '"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">' . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['name', 'quantity', 'discount_price', 'action'])
            ->toJson();
    }
}
