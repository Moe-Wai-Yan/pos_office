@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Products</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="bi bi-plus-lg"></i> Add Product
        </button>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Products Table --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Has Variants</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $key => $product)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->supplier->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $product->has_variants ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->has_variants ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-active" type="checkbox"
                                        data-id="{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="editProduct({{ $product }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="deleteProduct({{ $product->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No products found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form id="productForm" action="{{ route('products.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="productModalLabel">Add Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- BASIC PRODUCT INFO --}}
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
              <option value="">Select Category</option>
              @foreach ($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-select" required>
              <option value="">Select Supplier</option>
              @foreach ($suppliers as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Default Expiry (days)</label>
            <input type="number" name="default_expiry_days" id="default_expiry_days" class="form-control">
          </div>

          <div class="col-md-3 d-flex align-items-center mt-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="has_variants" id="has_variants">
              <label class="form-check-label fw-bold">Has Variants?</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" rows="2" class="form-control"></textarea>
          </div>
        </div>

        {{-- VARIANTS SECTION --}}
        <div id="variantsSection" class="mt-4 d-none">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Variants</h6>
            <button type="button" class="btn btn-sm btn-outline-success" id="addVariantRow">
              <i class="bi bi-plus-lg"></i> Add Variant
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>SKU</th>
                  <th>Barcode</th>
                  <th>Size</th>
                  <th>Color</th>
                  <th>Cost</th>
                  <th>Price</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="variantRows"></tbody>
            </table>
          </div>
        </div>

        {{-- UNITS SECTION --}}
        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Units</h6>
            <button type="button" class="btn btn-sm btn-outline-success" id="addUnitRow">
              <i class="bi bi-plus-lg"></i> Add Unit
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Unit</th>
                  <th>Sell Price</th>
                  <th>Default</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="unitRows"></tbody>
            </table>
          </div>
        </div>

        {{-- PRICES SECTION --}}
        <div class="mt-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Price Lists</h6>
            <button type="button" class="btn btn-sm btn-outline-success" id="addPriceRow">
              <i class="bi bi-plus-lg"></i> Add Price
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Price Name</th>
                  <th>Currency</th>
                  <th>Price</th>
                  <th>Effective From</th>
                  <th>Effective To</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="priceRows"></tbody>
            </table>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Product</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

{{-- TEMPLATES --}}
<template id="variantRowTemplate">
  <tr>
    <td><input type="text" name="variants[__index__][sku]" class="form-control" required></td>
    <td><input type="text" name="variants[__index__][barcode]" class="form-control"></td>
    <td><input type="text" name="variants[__index__][size]" class="form-control"></td>
    <td><input type="text" name="variants[__index__][color]" class="form-control"></td>
    <td><input type="number" step="0.01" name="variants[__index__][cost]" class="form-control"></td>
    <td><input type="number" step="0.01" name="variants[__index__][price]" class="form-control"></td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger remove-variant"><i class="bi bi-x-lg"></i></button>
    </td>
  </tr>
</template>

<template id="unitRowTemplate">
  <tr>
    <td>
      <select name="units[__index__][unit_id]" class="form-select" required>
        <option value="">Select Unit</option>
        @foreach ($units as $id => $code)
          <option value="{{ $id }}">{{ $code }}</option>
        @endforeach
      </select>
    </td>
    <td><input type="number" step="0.01" name="units[__index__][sell_price]" class="form-control" required></td>
    <td class="text-center">
      <input type="checkbox" name="units[__index__][is_default]" value="1" class="form-check-input">
    </td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger remove-unit"><i class="bi bi-x-lg"></i></button>
    </td>
  </tr>
</template>

<template id="priceRowTemplate">
  <tr>
    <td><input type="text" name="prices[__index__][price_name]" class="form-control" required></td>
    <td><input type="text" name="prices[__index__][currency]" value="USD" class="form-control" required></td>
    <td><input type="number" step="0.01" name="prices[__index__][price]" class="form-control" required></td>
    <td><input type="date" name="prices[__index__][effective_from]" class="form-control"></td>
    <td><input type="date" name="prices[__index__][effective_to]" class="form-control"></td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger remove-price"><i class="bi bi-x-lg"></i></button>
    </td>
  </tr>
</template>




{{-- Variant Row Template --}}
<template id="variantRowTemplate">
  <tr>
    <td><input type="text" name="variants[__index__][sku]" class="form-control" required></td>
    <td><input type="text" name="variants[__index__][barcode]" class="form-control"></td>
    <td><input type="text" name="variants[__index__][size]" class="form-control"></td>
    <td><input type="text" name="variants[__index__][color]" class="form-control"></td>
    <td><input type="number" step="0.01" name="variants[__index__][cost]" class="form-control"></td>
    <td><input type="number" step="0.01" name="variants[__index__][price]" class="form-control"></td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
        <i class="bi bi-x-lg"></i>
      </button>
    </td>
  </tr>
</template>




@endsection

@section('script')
<script>
    function editProduct(product) {
        $('#productModalLabel').text('Edit Product');
        $('#productForm').attr('action', '/products/' + product.id);
        $('#productForm').append('<input type="hidden" name="_method" value="PUT">');
        $('#name').val(product.name);
        $('#category_id').val(product.category_id);
        $('#supplier_id').val(product.supplier_id);
        $('#has_variants').prop('checked', product.has_variants);
        $('#default_expiry_days').val(product.default_expiry_days);
        $('#description').val(product.description);
        $('#productModal').modal('show');
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            fetch(`/products/${id}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            }).then(res => location.reload());
        }
    }


  document.addEventListener('DOMContentLoaded', () => {
    const hasVariants = document.getElementById('has_variants');
    const variantsSection = document.getElementById('variantsSection');
    const variantRows = document.getElementById('variantRows');
    const addVariantBtn = document.getElementById('addVariantRow');
    const rowTemplate = document.getElementById('variantRowTemplate').innerHTML;
    let index = 0;

    // Toggle variant section
    hasVariants.addEventListener('change', () => {
      variantsSection.classList.toggle('d-none', !hasVariants.checked);
    });

    // Add variant row
    addVariantBtn.addEventListener('click', () => {
      const newRow = rowTemplate.replace(/__index__/g, index++);
      variantRows.insertAdjacentHTML('beforeend', newRow);
    });

    // Remove variant row
    variantRows.addEventListener('click', (e) => {
      if (e.target.closest('.remove-variant')) {
        e.target.closest('tr').remove();
      }
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
  const toggleSection = (checkbox, sectionId) => {
    document.getElementById(sectionId).classList.toggle('d-none', !checkbox.checked);
  };

  const dynamicTable = (btnId, rowId, templateId, removeClass) => {
    const tableBody = document.getElementById(rowId);
    const template = document.getElementById(templateId).innerHTML;
    let i = 0;
    document.getElementById(btnId).addEventListener('click', () => {
      const newRow = template.replace(/__index__/g, i++);
      tableBody.insertAdjacentHTML('beforeend', newRow);
    });
    tableBody.addEventListener('click', e => {
      if (e.target.closest(removeClass)) e.target.closest('tr').remove();
    });
  };

  // toggle variant section
  const hasVariants = document.getElementById('has_variants');
  hasVariants.addEventListener('change', () => toggleSection(hasVariants, 'variantsSection'));

  // dynamic rows
  dynamicTable('addVariantRow', 'variantRows', 'variantRowTemplate', '.remove-variant');
  dynamicTable('addUnitRow', 'unitRows', 'unitRowTemplate', '.remove-unit');
  dynamicTable('addPriceRow', 'priceRows', 'priceRowTemplate', '.remove-price');
});

</script>
@endsection
