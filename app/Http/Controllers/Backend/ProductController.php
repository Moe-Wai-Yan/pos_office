<?php

namespace App\Http\Controllers\Backend;

use Exception;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\ProductSize;
use App\Models\ProductUnit;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\VariationType;
use App\Models\VolumePricing;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\VariantAttribute;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{

    public function index()
    {
       $products = Product::with(['category', 'supplier'])->latest()->get();
        $categories = Category::pluck('name', 'id');
        $suppliers = Supplier::pluck('name', 'id');
        $units=Unit::all();
        return view('products.index', compact('products','categories','suppliers','units'));
    }

    public function create()
    {

        $units = Unit::all();
        return view('products.create', compact('attributes', 'units'));
    }


    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'has_variants' => 'boolean',
        'description' => 'nullable|string',
        'variants' => 'array',
        'units' => 'array',
        'prices' => 'array',
    ]);

    $product = Product::create($validated);

    // Variants
    if ($request->has_variants && $request->variants) {
        foreach ($request->variants as $variant) {
            $product->variants()->create([
                'sku' => $variant['sku'],
                'barcode' => $variant['barcode'] ?? null,
                'default_cost' => $variant['cost'] ?? 0,
                'default_price' => $variant['price'] ?? 0,
                'is_active' => true,
            ]);
        }
    }

    // Units
    if ($request->units) {
        foreach ($request->units as $unit) {
            $product->productUnits()->create([
                'unit_id' => $unit['unit_id'],
                'sell_price' => $unit['sell_price'],
                'is_default' => isset($unit['is_default']),
            ]);
        }
    }

    // Prices
    if ($request->prices) {
        foreach ($request->prices as $price) {
            $product->productPrices()->create([
                'price_name' => $price['price_name'],
                'currency' => $price['currency'],
                'price' => $price['price'],
                'effective_from' => $price['effective_from'] ?? null,
                'effective_to' => $price['effective_to'] ?? null,
                'is_active' => true,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Product created successfully!');
}



}

