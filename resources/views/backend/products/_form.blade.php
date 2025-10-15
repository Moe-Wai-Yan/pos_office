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
            <select name="category_id" class="form-select">
              <option value="">Select Category</option>
              @foreach($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-bold">Supplier</label>
            <select name="supplier_id" class="form-select">
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
        </div>

        <hr>

        {{-- Variants Section --}}
        <div id="variantsSection" class="d-none">
          <h6 class="fw-bold">Variants</h6>
          <div class="table-responsive">
            <table class="table table-bordered">
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
            <button type="button" id="addVariant" class="btn btn-outline-success btn-sm">
              <i class="bi bi-plus"></i> Add Variant
            </button>
          </div>
        </div>

        {{-- Units Section --}}
        <hr>
        <h6 class="fw-bold">Units</h6>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Unit</th>
                <th>Sell Price</th>
                <th>Default?</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="unitRows"></tbody>
          </table>
          <button type="button" id="addUnit" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus"></i> Add Unit
          </button>
        </div>

        {{-- Prices Section --}}
        <hr>
        <h6 class="fw-bold">Custom Prices</h6>
        <div class="table-responsive">
          <table class="table table-bordered">
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
          <button type="button" id="addPrice" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-plus"></i> Add Price
          </button>
        </div>

      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- JS for dynamic rows --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const hasVariants = document.getElementById('has_variants');
  const variantsSection = document.getElementById('variantsSection');
  const variantRows = document.getElementById('variantRows');
  const addVariant = document.getElementById('addVariant');
  const unitRows = document.getElementById('unitRows');
  const addUnit = document.getElementById('addUnit');
  const priceRows = document.getElementById('priceRows');
  const addPrice = document.getElementById('addPrice');
  let vIndex = 0, uIndex = 0, pIndex = 0;

  hasVariants.addEventListener('change', () => {
    variantsSection.classList.toggle('d-none', !hasVariants.checked);
  });

  addVariant.addEventListener('click', () => {
    variantRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td><input name="variants[${vIndex}][sku]" class="form-control"></td>
        <td><input name="variants[${vIndex}][barcode]" class="form-control"></td>
        <td><input name="variants[${vIndex}][size]" class="form-control"></td>
        <td><input name="variants[${vIndex}][color]" class="form-control"></td>
        <td><input name="variants[${vIndex}][cost]" type="number" step="0.01" class="form-control"></td>
        <td><input name="variants[${vIndex}][price]" type="number" step="0.01" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    vIndex++;
  });

  addUnit.addEventListener('click', () => {
    unitRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td>
          <select name="units[${uIndex}][unit_id]" class="form-select">
            @foreach ($units as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </td>
        <td><input name="units[${uIndex}][sell_price]" type="number" step="0.01" class="form-control"></td>
        <td><input name="units[${uIndex}][is_default]" type="checkbox" value="1"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    uIndex++;
  });

  addPrice.addEventListener('click', () => {
    priceRows.insertAdjacentHTML('beforeend', `
      <tr>
        <td><input name="prices[${pIndex}][price_name]" class="form-control"></td>
        <td><input name="prices[${pIndex}][currency]" class="form-control" value="USD"></td>
        <td><input name="prices[${pIndex}][price]" type="number" step="0.01" class="form-control"></td>
        <td><input name="prices[${pIndex}][effective_from]" type="date" class="form-control"></td>
        <td><input name="prices[${pIndex}][effective_to]" type="date" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
      </tr>
    `);
    pIndex++;
  });

  document.body.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
      e.target.closest('tr').remove();
    }
  });
});
</script>
@endpush
