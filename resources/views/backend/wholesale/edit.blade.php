@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('wholesale') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Volume Price ကိုပြုပြင်မည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-9">
                            <form method="POST" action="{{ route('wholesale.update', $wholesale->id) }}"
                                id="wholesale_edit">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="products" class="form-label">Product</label>
                                            <select name="product_id" id="products" class="form-select"
                                                onchange="fetchVariations(event)">
                                                <option disabled hidden selected>Choose a product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $wholesale->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-4">
                                            <label class="form-label">Variation</label>
                                            <select name="product_variation_id" id="variation" class="form-select">
                                                <option hidden selected disabled>Select</option>
                                                @foreach ($variations as $variation)
                                                    <option value="{{ $variation->id }}"
                                                        {{ $variation->id == $wholesale->product_variation_id ? 'selected' : '' }}>
                                                        {{ $variation->variation->name }} : {{ $variation->type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-4">
                                            <label class="form-label">Option</label>
                                            <select name="option_type_id" id="variation-option" class="form-select">
                                                <option hidden selected disabled>Select</option>
                                                @foreach ($variationTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $type->id == $wholesale->option_type_id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="quantity" class="form-label">Target Quantity</label>
                                        <input type="number" name="quantity" id="quantity"
                                            placeholder="Enter target quantity" value="{{ $wholesale->quantity }}" required
                                            class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="discount-price" class="form-label">Discount Price</label>
                                        <input type="number" name="discount_price" id="discount-price"
                                            placeholder="Enter Discount Price" value="{{ $wholesale->discount_price }}"
                                            required class="form-control">
                                    </div>
                                </div>
                                <div class="text-end submit-m-btn">
                                    <button type="submit" class="submit-btn">Volume Price ကိုပြုပြင်မည်</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {!! JsValidator::formRequest('App\Http\Requests\StoreVolumePriceRequest', '#wholesale_edit') !!}

    <script>
        $(document).ready(function() {
            $('#upload_img').on('change', function() {
                let file_length = document.getElementById('upload_img').files.length;
                if (file_length > 0) {
                    $('.preview_img').html('');
                    for (i = 0; i < file_length; i++) {
                        $('.preview_img').html('');
                        $('.preview_img').append(
                            `<img src="${URL.createObjectURL(event.target.files[i])}" width=150 height =150/>`
                        )
                    }
                } else {
                    $('.preview_img').html(
                        `<img src="{{ asset(config('app.companyInfo.logo')) }}" width=150 height=150 alt="">`
                    );
                }
            })
        })

        function fetchVariations(event) {
            let variationSelect = event.target.closest('.row').querySelector('#variation')
            variationSelect.innerHTML = ''
            let id = event.target.value;
            option = "<option selected hidden disabled>------- Loading -------</option>"
            variationSelect.innerHTML = option
            $.ajax({
                url: `/products/${id}/variations`,
                method: 'GET',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                },
            }).done(function(res) {
                let variations = res.variations;
                console.log(variations);
                if (variations && variations.length != 0) {
                    variationSelect.innerHTML = ''
                    variations.forEach(variation => {
                        let option = document.createElement('option')
                        option.textContent = `${variation.variation_name} : ${variation.type_name}`
                        option.value = variation.id
                        variationSelect.appendChild(option)
                    });
                    variationSelect.classList.remove('is-invalid');
                    fetchVariationOptions(event.target)
                } else {
                    option = "<option selected hidden disabled>There's no variation within this product</option>"
                    variationSelect.classList.add('is-invalid');
                    variationSelect.innerHTML = option
                }
            })
        }

        function fetchVariationOptions(element) {
            let optionSelect = element.closest('.row').querySelector('#variation-option')
            optionSelect.innerHTML = ''
            let id = document.getElementById('variation').value;
            option = "<option selected hidden disabled>------- Loading -------</option>"
            optionSelect.innerHTML = option
            $.ajax({
                url: `/variations/${id}/options`,
                method: 'GET',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                },
            }).done(function(res) {
                let variationOptions = res.options;
                console.log(variationOptions);
                if (variationOptions && variationOptions.length != 0) {
                    optionSelect.innerHTML = ''
                    variationOptions.forEach(variationOption => {
                        let option = document.createElement('option')
                        option.textContent = variationOption.name
                        option.value = variationOption.id
                        optionSelect.appendChild(option)
                    });
                    optionSelect.classList.remove('is-invalid');
                } else {
                    option =
                        "<option selected hidden disabled>There's no variation option within this product</option>"
                    optionSelect.classList.add('is-invalid');
                    optionSelect.innerHTML = option
                }
            })
        }
    </script>
@endsection
