# Laravel 9 — Product CRUD (Products, Variants, Attributes, Prices, Units)

This document contains migrations, models, controller, routes, and Blade UI for a Product CRUD system that supports:
- Products (master record)
- ProductVariants (every sellable SKU — single product = one default variant)
- ProductAttributes + VariantAttributes
- ProductPrices (tiered prices)
- Units + ProductUnits (UOM per variant)

Assumptions:
- You already have `suppliers`, `categories`, and `tax_rates` tables/models.
- Using Laravel 9 + Bootstrap 5 + jQuery (for small dynamic UI helpers).

---

## 1) Migrations

Create these migrations. Run `php artisan make:migration create_products_table` etc. Paste contents below.

### database/migrations/2025_01_01_000001_create_products_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->boolean('has_variants')->default(false);
            $table->integer('default_expiry_days')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('products');
    }
};
```


### database/migrations/2025_01_01_000002_create_product_variants_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('default_cost', 15, 4)->nullable();
            $table->decimal('default_price', 15, 4)->nullable();
            $table->string('default_currency', 3)->nullable();
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('product_variants');
    }
};
```


### database/migrations/2025_01_01_000003_create_product_attributes_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id('attribute_id');
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('product_attributes');
    }
};
```


### database/migrations/2025_01_01_000004_create_variant_attributes_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id('variant_attr_id');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();

            $table->unique(['variant_id', 'attribute_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('variant_attributes');
    }
};
```


### database/migrations/2025_01_01_000005_create_product_prices_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->string('price_name')->nullable();
            $table->string('currency', 3)->nullable();
            $table->decimal('price', 15, 4);
            $table->dateTime('effective_from')->nullable();
            $table->dateTime('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('product_prices');
    }
};
```


### database/migrations/2025_01_01_000006_create_units_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('units', function (Blueprint $table) {
            $table->id('unit_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('base_ratio', 12, 4)->default(1);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('units');
    }
};
```


### database/migrations/2025_01_01_000007_create_product_units_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id('product_unit_id');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('sell_price', 15, 4)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['variant_id', 'unit_id']);
        });
    }

    public function down() {
        Schema::dropIfExists('product_units');
    }
};
```

---

## 2) Models

### app/Models/Product.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = ['name','category_id','supplier_id','has_variants','default_expiry_days','description','is_active'];

    public function variants(){
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    public function defaultVariant(){
        return $this->hasOne(ProductVariant::class, 'product_id', 'product_id')->where('is_default', true);
    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
```


### app/Models/ProductVariant.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $primaryKey = 'variant_id';
    protected $fillable = ['product_id','name','sku','barcode','default_cost','default_price','default_currency','tax_rate_id','is_active','is_default'];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function attributes(){
        return $this->hasMany(VariantAttribute::class, 'variant_id', 'variant_id');
    }

    public function prices(){
        return $this->hasMany(ProductPrice::class, 'variant_id', 'variant_id');
    }

    public function units(){
        return $this->hasMany(ProductUnit::class, 'variant_id', 'variant_id');
    }
}
```


### app/Models/ProductAttribute.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $primaryKey = 'attribute_id';
    protected $fillable = ['name','is_active'];
}
```


### app/Models/VariantAttribute.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $primaryKey = 'variant_attr_id';
    protected $fillable = ['variant_id','attribute_id','value'];

    public function attribute(){
        return $this->belongsTo(ProductAttribute::class, 'attribute_id', 'attribute_id');
    }
}
```


### app/Models/ProductPrice.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $primaryKey = 'price_id';
    protected $fillable = ['variant_id','price_name','currency','price','effective_from','effective_to','is_active'];

    protected $casts = [
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
    ];
}
```


### app/Models/Unit.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $primaryKey = 'unit_id';
    protected $fillable = ['code','name','base_ratio'];
}
```


### app/Models/ProductUnit.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_unit_id';
    protected $fillable = ['variant_id','unit_id','sell_price','is_default'];

    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }
}
```

---

## 3) Controller (Products)

Create `php artisan make:controller ProductController --resource` and replace contents with the following. This controller handles product create/edit including nested arrays for variants, prices, and units.

### app/Http/Controllers/ProductController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\VariantAttribute;
use App\Models\ProductPrice;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $attributes = ProductAttribute::where('is_active', true)->get();
        $units = Unit::all();
        return view('products.create', compact('attributes','units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'has_variants' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        $product = Product::create(array_merge($data, ['has_variants' => $request->has('has_variants') ? (bool)$request->has_variants : false]));

        // Variants input is an array of variants
        $variants = $request->input('variants', []);
        if (empty($variants)) {
            // create a default variant from product-level fields if provided
            $v = new ProductVariant();
            $v->product_id = $product->product_id;
            $v->name = $request->input('variant_name') ?? $product->name;
            $v->sku = $request->input('sku') ?? null;
            $v->barcode = $request->input('barcode') ?? null;
            $v->default_cost = $request->input('default_cost') ?? null;
            $v->default_price = $request->input('default_price') ?? null;
            $v->default_currency = $request->input('default_currency') ?? null;
            $v->tax_rate_id = $request->input('tax_rate_id') ?? null;
            $v->is_default = true;
            $v->save();
        } else {
            foreach ($variants as $idx => $var) {
                $v = ProductVariant::create([
                    'product_id' => $product->product_id,
                    'name' => $var['name'] ?? null,
                    'sku' => $var['sku'] ?? null,
                    'barcode' => $var['barcode'] ?? null,
                    'default_cost' => $var['default_cost'] ?? null,
                    'default_price' => $var['default_price'] ?? null,
                    'default_currency' => $var['default_currency'] ?? null,
                    'tax_rate_id' => $var['tax_rate_id'] ?? null,
                    'is_default' => isset($var['is_default']) && $var['is_default'] ? true : false,
                ]);

                // attributes
                if (!empty($var['attributes'])) {
                    foreach ($var['attributes'] as $attrId => $value) {
                        if ($value === null || $value === '') continue;
                        VariantAttribute::create([
                            'variant_id' => $v->variant_id,
                            'attribute_id' => $attrId,
                            'value' => $value,
                        ]);
                    }
                }

                // prices
                if (!empty($var['prices'])) {
                    foreach ($var['prices'] as $priceRow) {
                        ProductPrice::create([
                            'variant_id' => $v->variant_id,
                            'price_name' => $priceRow['price_name'] ?? null,
                            'currency' => $priceRow['currency'] ?? null,
                            'price' => $priceRow['price'] ?? 0,
                            'effective_from' => $priceRow['effective_from'] ?? null,
                            'effective_to' => $priceRow['effective_to'] ?? null,
                            'is_active' => isset($priceRow['is_active']) ? (bool)$priceRow['is_active'] : true,
                        ]);
                    }
                }

                // units
                if (!empty($var['units'])) {
                    foreach ($var['units'] as $unitRow) {
                        ProductUnit::create([
                            'variant_id' => $v->variant_id,
                            'unit_id' => $unitRow['unit_id'],
                            'sell_price' => $unitRow['sell_price'] ?? null,
                            'is_default' => isset($unitRow['is_default']) ? (bool)$unitRow['is_default'] : false,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created');
    }

    public function show(Product $product)
    {
        $product->load('variants.attributes.attribute','variants.prices','variants.units.unit');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('variants.attributes.attribute','variants.prices','variants.units.unit');
        $attributes = ProductAttribute::where('is_active', true)->get();
        $units = Unit::all();
        return view('products.edit', compact('product','attributes','units'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'has_variants' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        $product->update(array_merge($data, ['has_variants' => $request->has('has_variants') ? (bool)$request->has_variants : false]));

        // For simplicity this example deletes existing variants and recreates from the request.
        // In production you may want to diff and update instead.
        $product->variants()->delete();

        $variants = $request->input('variants', []);
        foreach ($variants as $var) {
            $v = ProductVariant::create([
                'product_id' => $product->product_id,
                'name' => $var['name'] ?? null,
                'sku' => $var['sku'] ?? null,
                'barcode' => $var['barcode'] ?? null,
                'default_cost' => $var['default_cost'] ?? null,
                'default_price' => $var['default_price'] ?? null,
                'default_currency' => $var['default_currency'] ?? null,
                'tax_rate_id' => $var['tax_rate_id'] ?? null,
                'is_default' => isset($var['is_default']) && $var['is_default'] ? true : false,
            ]);

            if (!empty($var['attributes'])) {
                foreach ($var['attributes'] as $attrId => $value) {
                    if ($value === null || $value === '') continue;
                    VariantAttribute::create([
                        'variant_id' => $v->variant_id,
                        'attribute_id' => $attrId,
                        'value' => $value,
                    ]);
                }
            }

            if (!empty($var['prices'])) {
                foreach ($var['prices'] as $priceRow) {
                    ProductPrice::create([
                        'variant_id' => $v->variant_id,
                        'price_name' => $priceRow['price_name'] ?? null,
                        'currency' => $priceRow['currency'] ?? null,
                        'price' => $priceRow['price'] ?? 0,
                        'effective_from' => $priceRow['effective_from'] ?? null,
                        'effective_to' => $priceRow['effective_to'] ?? null,
                        'is_active' => isset($priceRow['is_active']) ? (bool)$priceRow['is_active'] : true,
                    ]);
                }
            }

            if (!empty($var['units'])) {
                foreach ($var['units'] as $unitRow) {
                    ProductUnit::create([
                        'variant_id' => $v->variant_id,
                        'unit_id' => $unitRow['unit_id'],
                        'sell_price' => $unitRow['sell_price'] ?? null,
                        'is_default' => isset($unitRow['is_default']) ? (bool)$unitRow['is_default'] : false,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }
}
```

---

## 4) Routes

Add to `routes/web.php`:

```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

---

## 5) Blade Views (Bootstrap 5 + simple JS)

Create these views: `resources/views/products/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, and include partial `_variant_row.blade.php`.

### resources/views/products/index.blade.php
```blade
@extends('main')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Products</h3>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Create Product</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Category</th>
        <th>Supplier</th>
        <th>Variants</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $product)
        <tr>
          <td>{{ $product->product_id }}</td>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category->name ?? '-' }}</td>
          <td>{{ $product->supplier->name ?? '-' }}</td>
          <td>
            @foreach($product->variants as $v)
              <div><strong>{{ $v->sku ?? '—' }}</strong> — {{ $v->name ?? 'Default' }} ({{ $v->default_price ?? '—' }})</div>
            @endforeach
          </td>
          <td>
            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">View</a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete product?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{ $products->links() }}
</div>
@endsection
```


### resources/views/products/create.blade.php
```blade
@extends('main')
@section('content')
<div class="container">
  <h3>Create Product</h3>

  <form action="{{ route('products.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          @foreach(\App\Models\Category::all() as $cat)
            <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select">
          <option value="">— None —</option>
          @foreach(\App\Models\Supplier::all() as $s)
            <option value="{{ $s->supplier_id }}">{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="hasVariants" name="has_variants" value="1">
      <label class="form-check-label" for="hasVariants">Has variants</label>
    </div>

    <hr>

    <h5>Variants / Single variant</h5>
    <small class="text-muted">If you don't create any variant rows below, a single default variant will be created.</small>

    <div id="variantsWrapper"></div>

    <div class="mt-2">
      <button type="button" class="btn btn-sm btn-secondary" id="addVariantBtn">+ Add Variant</button>
    </div>

    <hr>

    <button class="btn btn-primary mt-3">Save Product</button>
  </form>
</div>

<script type="text/template" id="variant-row-template">
  <div class="card mb-2 variant-row p-3">
    <div class="d-flex justify-content-between">
      <h6>Variant</h6>
      <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
    </div>

    <div class="row">
      <div class="col-md-4 mb-2">
        <label class="form-label">Name</label>
        <input name="variants[__INDEX__][name]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">SKU</label>
        <input name="variants[__INDEX__][sku]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Barcode</label>
        <input name="variants[__INDEX__][barcode]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Cost</label>
        <input name="variants[__INDEX__][default_cost]" class="form-control" type="number" step="0.01">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Price</label>
        <input name="variants[__INDEX__][default_price]" class="form-control" type="number" step="0.01">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Currency</label>
        <input name="variants[__INDEX__][default_currency]" class="form-control">
      </div>

      <div class="col-12 mt-2">
        <h6>Attributes</h6>
        @foreach(\App\Models\ProductAttribute::where('is_active', true)->get() as $attr)
          <div class="mb-2">
            <label class="form-label">{{ $attr->name }}</label>
            <input name="variants[__INDEX__][attributes][{{ $attr->attribute_id }}]" class="form-control">
          </div>
        @endforeach
      </div>

      <div class="col-12 mt-2">
        <h6>Prices</h6>
        <div class="price-rows"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary add-price">+ Add Price</button>
      </div>

      <div class="col-12 mt-2">
        <h6>Units</h6>
        <div class="unit-rows"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary add-unit">+ Add Unit</button>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="price-row-template">
  <div class="input-group mb-2 price-row">
    <input name="variants[__INDEX__][prices][__PINDEX__][price_name]" placeholder="Label (Retail)" class="form-control">
    <input name="variants[__INDEX__][prices][__PINDEX__][currency]" placeholder="CUR" class="form-control">
    <input name="variants[__INDEX__][prices][__PINDEX__][price]" type="number" step="0.01" placeholder="Price" class="form-control">
    <button type="button" class="btn btn-danger remove-price">X</button>
  </div>
</script>

<script type="text/template" id="unit-row-template">
  <div class="input-group mb-2 unit-row">
    <select name="variants[__INDEX__][units][__UINDEX__][unit_id]" class="form-select">
      @foreach(\App\Models\Unit::all() as $u)
        <option value="{{ $u->unit_id }}">{{ $u->code }} — {{ $u->name }}</option>
      @endforeach
    </select>
    <input name="variants[__INDEX__][units][__UINDEX__][sell_price]" type="number" step="0.01" class="form-control" placeholder="Sell price">
    <label class="input-group-text"><input type="checkbox" name="variants[__INDEX__][units][__UINDEX__][is_default]" value="1"> default</label>
    <button type="button" class="btn btn-danger remove-unit">X</button>
  </div>
</script>

<script>
$(function(){
  $(document).on('click', '.add-price', function(){
    const wrapper = $(this).closest('.card').find('.price-rows');
    const vCard = $(this).closest('.variant-row');
    const idx = $('.variant-row').index(vCard);
    let pIndex = wrapper.data('pindex') || 0;
    let tpl = $('#price-row-template').html();
    tpl = tpl.replace(/__INDEX__/g, idx).replace(/__PINDEX__/g, pIndex);
    wrapper.append(tpl);
    wrapper.data('pindex', pIndex + 1);
  });

  $(document).on('click', '.remove-price', function(){
    $(this).closest('.price-row').remove();
  });

  $(document).on('click', '.add-unit', function(){
    const wrapper = $(this).closest('.card').find('.unit-rows');
    const vCard = $(this).closest('.variant-row');
    const idx = $('.variant-row').index(vCard);
    let uIndex = wrapper.data('uindex') || 0;
    let tpl = $('#unit-row-template').html();
    tpl = tpl.replace(/__INDEX__/g, idx).replace(/__UINDEX__/g, uIndex);
    wrapper.append(tpl);
    wrapper.data('uindex', uIndex + 1);
  });

  $(document).on('click', '.remove-unit', function(){
    $(this).closest('.unit-row').remove();
  });
});
</script>

@section('script')
<script>
$(function(){
  let variantIndex = 0;
  $('#addVariantBtn').on('click', function(){
    const tpl = $('#variant-row-template').html();
    const html = tpl.replace(/__INDEX__/g, variantIndex);
    $('#variantsWrapper').append(html);
    variantIndex++;
  });

  $(document).on('click', '.remove-variant', function(){
    $(this).closest('.variant-row').remove();
  });
});
</script>
@endsection
@endsection
```


### resources/views/products/_variant_row_template.blade.php
```blade

```


### resources/views/products/edit.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container">
  <h3>Edit Product</h3>

  <form action="{{ route('products.update', $product) }}" method="POST">
    @csrf @method('PUT')

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="{{ $product->name }}" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          @foreach(\App\Models\Category::all() as $cat)
            <option value="{{ $cat->category_id }}" {{ $product->category_id == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select">
          <option value="">— None —</option>
          @foreach(\App\Models\Supplier::all() as $s)
            <option value="{{ $s->supplier_id }}" {{ $product->supplier_id == $s->supplier_id ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="hasVariants" name="has_variants" value="1" {{ $product->has_variants ? 'checked' : '' }}>
      <label class="form-check-label" for="hasVariants">Has variants</label>
    </div>

    <hr>

    <h5>Variants</h5>
    <div id="variantsWrapper">
      @foreach($product->variants as $i => $v)
        <div class="card mb-2 variant-row p-3">
          <div class="d-flex justify-content-between">
            <h6>Variant</h6>
            <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
          </div>

          <div class="row">
            <div class="col-md-4 mb-2">
              <label class="form-label">Name</label>
              <input name="variants[{{ $i }}][name]" class="form-control" value="{{ $v->name }}">
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">SKU</label>
              <input name="variants[{{ $i }}][sku]" class="form-control" value="{{ $v->sku }}">
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Barcode</label>
              <input name="variants[{{ $i }}][barcode]" class="form-control" value="{{ $v->barcode }}">
            </div>

            <div class="col-md-4 mb-2">
              <label class="form-label">Cost</label>
              <input name="variants[{{ $i }}][default_cost]" class="form-control" type="number" step="0.01" value="{{ $v->default_cost }}">
            </div>

            <div class="col-md-4 mb-2">
              <label class="form-label">Price</label>
              <input name="variants[{{ $i }}][default_price]" class="form-control" type="number" step="0.01" value="{{ $v->default_price }}">
            </div>

            <div class="col-md-4 mb-2">
              <label class="form-label">Currency</label>
              <input name="variants[{{ $i }}][default_currency]" class="form-control" value="{{ $v->default_currency }}">
            </div>

            <div class="col-12 mt-2">
              <h6>Attributes</h6>
              @foreach($attributes as $attr)
                <div class="mb-2">
                  <label class="form-label">{{ $attr->name }}</label>
                  <input name="variants[{{ $i }}][attributes][{{ $attr->attribute_id }}]" class="form-control" value="{{ optional($v->attributes->where('attribute_id', $attr->attribute_id)->first())->value }}">
                </div>
              @endforeach
            </div>

            <div class="col-12 mt-2">
              <h6>Prices</h6>
              <div class="price-rows">
                @foreach($v->prices as $pi => $pr)
                  <div class="input-group mb-2 price-row">
                    <input name="variants[{{ $i }}][prices][{{ $pi }}][price_name]" value="{{ $pr->price_name }}" class="form-control">
                    <input name="variants[{{ $i }}][prices][{{ $pi }}][currency]" value="{{ $pr->currency }}" class="form-control">
                    <input name="variants[{{ $i }}][prices][{{ $pi }}][price]" type="number" step="0.01" value="{{ $pr->price }}" class="form-control">
                    <button type="button" class="btn btn-danger remove-price">X</button>
                  </div>
                @endforeach
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary add-price">+ Add Price</button>
            </div>

            <div class="col-12 mt-2">
              <h6>Units</h6>
              <div class="unit-rows">
                @foreach($v->units as $ui => $uu)
                  <div class="input-group mb-2 unit-row">
                    <select name="variants[{{ $i }}][units][{{ $ui }}][unit_id]" class="form-select">
                      @foreach($units as $u)
                        <option value="{{ $u->unit_id }}" {{ $uu->unit_id == $u->unit_id ? 'selected' : '' }}>{{ $u->code }} — {{ $u->name }}</option>
                      @endforeach
                    </select>
                    <input name="variants[{{ $i }}][units][{{ $ui }}][sell_price]" type="number" step="0.01" class="form-control" value="{{ $uu->sell_price }}">
                    <label class="input-group-text"><input type="checkbox" name="variants[{{ $i }}][units][{{ $ui }}][is_default]" value="1" {{ $uu->is_default ? 'checked' : '' }}> default</label>
                    <button type="button" class="btn btn-danger remove-unit">X</button>
                  </div>
                @endforeach
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary add-unit">+ Add Unit</button>
            </div>

          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-2">
      <button type="button" class="btn btn-sm btn-secondary" id="addVariantBtn">+ Add Variant</button>
    </div>

    <hr>

    <button class="btn btn-primary mt-3">Update Product</button>
  </form>
</div>

<script type="text/template" id="variant-row-template">
  <div class="card mb-2 variant-row p-3">
    <div class="d-flex justify-content-between">
      <h6>Variant</h6>
      <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
    </div>

    <div class="row">
      <div class="col-md-4 mb-2">
        <label class="form-label">Name</label>
        <input name="variants[__INDEX__][name]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">SKU</label>
        <input name="variants[__INDEX__][sku]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Barcode</label>
        <input name="variants[__INDEX__][barcode]" class="form-control">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Cost</label>
        <input name="variants[__INDEX__][default_cost]" class="form-control" type="number" step="0.01">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Price</label>
        <input name="variants[__INDEX__][default_price]" class="form-control" type="number" step="0.01">
      </div>

      <div class="col-md-4 mb-2">
        <label class="form-label">Currency</label>
        <input name="variants[__INDEX__][default_currency]" class="form-control">
      </div>

      <div class="col-12 mt-2">
        <h6>Attributes</h6>
        @foreach(\App\Models\ProductAttribute::where('is_active', true)->get() as $attr)
          <div class="mb-2">
            <label class="form-label">{{ $attr->name }}</label>
            <input name="variants[__INDEX__][attributes][{{ $attr->attribute_id }}]" class="form-control">
          </div>
        @endforeach
      </div>

      <div class="col-12 mt-2">
        <h6>Prices</h6>
        <div class="price-rows"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary add-price">+ Add Price</button>
      </div>

      <div class="col-12 mt-2">
        <h6>Units</h6>
        <div class="unit-rows"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary add-unit">+ Add Unit</button>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="price-row-template">
  <div class="input-group mb-2 price-row">
    <input name="variants[__INDEX__][prices][__PINDEX__][price_name]" placeholder="Label (Retail)" class="form-control">
    <input name="variants[__INDEX__][prices][__PINDEX__][currency]" placeholder="CUR" class="form-control">
    <input name="variants[__INDEX__][prices][__PINDEX__][price]" type="number" step="0.01" placeholder="Price" class="form-control">
    <button type="button" class="btn btn-danger remove-price">X</button>
  </div>
</script>

<script type="text/template" id="unit-row-template">
  <div class="input-group mb-2 unit-row">
    <select name="variants[__INDEX__][units][__UINDEX__][unit_id]" class="form-select">
      @foreach(\App\Models\Unit::all() as $u)
        <option value="{{ $u->unit_id }}">{{ $u->code }} — {{ $u->name }}</option>
      @endforeach
    </select>
    <input name="variants[__INDEX__][units][__UINDEX__][sell_price]" type="number" step="0.01" class="form-control" placeholder="Sell price">
    <label class="input-group-text"><input type="checkbox" name="variants[__INDEX__][units][__UINDEX__][is_default]" value="1"> default</label>
    <button type="button" class="btn btn-danger remove-unit">X</button>
  </div>
</script>

<script>
$(function(){
  $(document).on('click', '.add-price', function(){
    const wrapper = $(this).closest('.card').find('.price-rows');
    const vCard = $(this).closest('.variant-row');
    const idx = $('.variant-row').index(vCard);
    let pIndex = wrapper.data('pindex') || 0;
    let tpl = $('#price-row-template').html();
    tpl = tpl.replace(/__INDEX__/g, idx).replace(/__PINDEX__/g, pIndex);
    wrapper.append(tpl);
    wrapper.data('pindex', pIndex + 1);
  });

  $(document).on('click', '.remove-price', function(){
    $(this).closest('.price-row').remove();
  });

  $(document).on('click', '.add-unit', function(){
    const wrapper = $(this).closest('.card').find('.unit-rows');
    const vCard = $(this).closest('.variant-row');
    const idx = $('.variant-row').index(vCard);
    let uIndex = wrapper.data('uindex') || 0;
    let tpl = $('#unit-row-template').html();
    tpl = tpl.replace(/__INDEX__/g, idx).replace(/__UINDEX__/g, uIndex);
    wrapper.append(tpl);
    wrapper.data('uindex', uIndex + 1);
  });

  $(document).on('click', '.remove-unit', function(){
    $(this).closest('.unit-row').remove();
  });
});
@section('script')

@endsection
</script>

<script>
$(function(){
  $('#addVariantBtn').on('click', function(){
    const tpl = $('#variant-row-template').html();
    $('#variantsWrapper').append(tpl.replace(/__INDEX__/g, $('.variant-row').length));
  });

  $(document).on('click', '.remove-variant', function(){
    $(this).closest('.variant-row').remove();
  });

  // price/unit handlers delegated in template
});
</script>
@endsection
```


### resources/views/products/show.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container">
  <h3>Product: {{ $product->name }}</h3>
  <p>{{ $product->description }}</p>

  <h5>Variants</h5>
  @foreach($product->variants as $v)
    <div class="card mb-2 p-2">
      <strong>{{ $v->name ?? 'Default' }}</strong> — SKU: {{ $v->sku }} — Price: {{ $v->default_price }}
      <div>Attributes:
        @foreach($v->attributes as $a)
          <span class="badge bg-secondary">{{ $a->attribute->name }}: {{ $a->value }}</span>
        @endforeach
      </div>

      <div>Prices:
        @foreach($v->prices as $p)
          <div>{{ $p->price_name }}: {{ $p->price }} {{ $p->currency }}</div>
        @endforeach
      </div>

      <div>Units:
        @foreach($v->units as $u)
          <div>{{ $u->unit->code }} — {{ $u->sell_price }} @if($u->is_default) (default) @endif</div>
        @endforeach
      </div>
    </div>
  @endforeach

  <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
```

---

## 6) Notes & Next steps

- The controller currently **recreates variants on update** (deletes existing). For production, implement diff-based update or use identifiable IDs to update/insert/delete variants.
- Add server-side validation rules for nested arrays if needed (Laravel supports `.*` validation keys).
- Consider using Livewire for a much nicer UX (dynamic variant rows without JS boilerplate).
- Add policies / authorization as appropriate.

---

If you want, I can:
- convert the nested form to use AJAX save endpoints per-variant, or
- produce a Livewire component for a smoother UX, or
- generate unit tests for the controller.

Tell me which one you want next and I’ll generate it.
