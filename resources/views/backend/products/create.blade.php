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
