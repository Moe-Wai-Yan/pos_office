@extends('main')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Products</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
      <i class="bi bi-plus-lg me-1"></i> Add Product
    </button>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Variants?</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td>{{ $product->supplier->name ?? '-' }}</td>
            <td>
              @if ($product->has_variants)
                <span class="badge bg-info">Yes</span>
              @else
                <span class="badge bg-secondary">No</span>
              @endif
            </td>
            <td>
              @if ($product->is_active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-danger">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
              <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i></a>
              <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

 {{-- Product Form --}}

 <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form id="productForm" action="{{ route('products.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="productModalLabel">Add Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- Product Info --}}
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Product Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Category</label>
            <select name="category_id" class="form-select" required>
              <option value="">Select Category</option>
              @foreach($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Supplier</label>
            <select name="supplier_id" class="form-select" required>
              <option value="">Select Supplier</option>
              @foreach($suppliers as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Default Expiry (days)</label>
            <input type="number" name="default_expiry_days" class="form-control">
          </div>

          <div class="col-md-3 d-flex align-items-center mt-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="has_variants" id="has_variants">
              <label class="form-check-label fw-bold">Has Variants?</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" rows="2" class="form-control"></textarea>
          </div>
        </div>

        <hr>

        {{-- Variant Section --}}
        <div id="variantsSection" class="d-none">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Variants</h6>
            <button type="button" class="btn btn-sm btn-outline-success" id="addVariantRow">
              <i class="bi bi-plus"></i> Add Variant
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>SKU</th>
                  <th>Barcode</th>
                  <th>Cost</th>
                  <th>Price</th>
                  <th>Attribute</th>
                  <th>Value</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="variantRows"></tbody>
            </table>
          </div>
        </div>

        {{-- Units + Prices (only for non-variant products) --}}
        <div id="unitPriceSection">
          <h6 class="fw-bold mt-3">Units</h6>
          <table class="table table-bordered">
            <thead class="table-light"><tr><th>Unit</th><th>Sell Price</th><th>Default?</th><th></th></tr></thead>
            <tbody id="unitRows"></tbody>
          </table>
          <button type="button" id="addUnit" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> Add Unit</button>

          <hr>
          <h6 class="fw-bold">Prices</h6>
          <table class="table table-bordered">
            <thead class="table-light"><tr><th>Name</th><th>Currency</th><th>Price</th><th>From</th><th>To</th><th></th></tr></thead>
            <tbody id="priceRows"></tbody>
          </table>
          <button type="button" id="addPrice" class="btn btn-sm btn-outline-warning"><i class="bi bi-plus"></i> Add Price</button>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

@section('script')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const hasVariants = document.getElementById('has_variants');
  const variantsSection = document.getElementById('variantsSection');
  const unitPriceSection = document.getElementById('unitPriceSection');
  const variantRows = document.getElementById('variantRows');
  const addVariantRow = document.getElementById('addVariantRow');
  const unitRows = document.getElementById('unitRows');
  const priceRows = document.getElementById('priceRows');
  const addUnit = document.getElementById('addUnit');
  const addPrice = document.getElementById('addPrice');

  let v = 0, u = 0, p = 0;

  hasVariants.addEventListener('change', () => {
    const checked = hasVariants.checked;
    variantsSection.classList.toggle('d-none', !checked);
    unitPriceSection.classList.toggle('d-none', checked);
  });

  addVariantRow.addEventListener('click', () => {
    variantRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td><input name="variants[${v}][sku]" class="form-control"></td>
        <td><input name="variants[${v}][barcode]" class="form-control"></td>
        <td><input name="variants[${v}][default_cost]" type="number" step="0.01" class="form-control"></td>
        <td><input name="variants[${v}][default_price]" type="number" step="0.01" class="form-control"></td>
        <td>
          <select name="variants[${v}][attribute_id]" class="form-select">
            <option value="">Select Attribute</option>
            @foreach($attributes as $attr)
              <option value="{{ $attr->id }}">{{ $attr->name }}</option>
            @endforeach
          </select>
        </td>
        <td><input name="variants[${v}][value]" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    v++;
  });

  addUnit.addEventListener('click', () => {
    unitRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td>
          <select name="units[${u}][unit_id]" class="form-select">
            @foreach($units as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }}</option>
            @endforeach
          </select>
        </td>
        <td><input name="units[${u}][sell_price]" type="number" step="0.01" class="form-control"></td>
        <td><input name="units[${u}][is_default]" type="checkbox" value="1"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    u++;
  });

  addPrice.addEventListener('click', () => {
    priceRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td><input name="prices[${p}][price_name]" class="form-control"></td>
        <td><input name="prices[${p}][currency]" class="form-control" value="USD"></td>
        <td><input name="prices[${p}][price]" type="number" step="0.01" class="form-control"></td>
        <td><input name="prices[${p}][effective_from]" type="date" class="form-control"></td>
        <td><input name="prices[${p}][effective_to]" type="date" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    p++;
  });

  document.body.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) e.target.closest('tr').remove();
  });
});
</script>
@endsection


{{-- End Product Form --}}


@endsection
