@extends('main')
@section('style')
    <script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('product') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Product အသစ်ဖန်တီးမည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-12">
                            <form method="POST" action="{{ route('product.update', $product->id) }}" id="product_create"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                {{-- <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-md form-switch-primary ms-2 mb-4 d-flex align-items-center">
                                        <input class="form-check-input mb-0" name="instock" type="checkbox" role="switch" id="SwitchCheck7" checked value="1">
                                        <label class="form-check-label mb-0" for="SwitchCheck7">Instock</label>
                                    </div>
                                </div>
                            </div> --}}
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-4">
                                            <label class="form-label">အမည်</label>
                                            <input type="text" class="form-control" name="name" autocomplete="off"
                                                value="{{ $product->name }}">
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="mb-4">
                                            <label class="form-label">အမျိုးအစား</label>
                                            <select name="product_type" class="form-select" id="product-type"
                                                onchange="changeProductType(this)">
                                                <option value="1" {{ $product->product_type == 1 ? 'selected' : '' }}>
                                                    Single</option>
                                                <option value="2" {{ $product->product_type == 2 ? 'selected' : '' }}>
                                                    Variation</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-4">
                                            <label for="category">အမျိူးအစား / Category</label>
                                            <select name="category_id" class="form-control"
                                                aria-label="Default select example" id='category'>
                                                <option selected disabled>Category ရွေးပါ</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                 @error('category_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>



                                     <div class="col-xl-6">
                                        <div class="mb-4">
                                            <label for="sub_category">SubCategory</label>
                                             <select name="sub_category_id" id="sub_category" class="form-control" {{ $subCategories->isEmpty() ? 'disabled' : '' }}>
                                                <option selected disabled>Sub Category ရွေးပါ</option>
                                                @foreach($subCategories as $sub)
                                                    <option value="{{ $sub->id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="mb-4">
                                            <label for="brand">အမှတ်တံဆိပ် / Brand</label>
                                            <select name="brand_id" class="form-control" aria-label="Default select example"
                                                id='brand'>
                                                <option selected disabled>Brand ရွေးပါ</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                 @error('brand_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                   @if ($product->product_type==1)
                                 <div class="col-xl-6">
                                    <div class="mb-4">
                                        <label class="form-label">Discount Price</label>
                                        <input type="text" class="form-control" name="discount_price" autocomplete="off"
                                            value="{{ $product->discount_price }}">
                                    </div>
                                </div>
                                   @endif

                                     <div class="row">
                                        <div class="col">
                                            <div class="mb-4 form-check">
                                                <input class="form-check-input" type="checkbox" name="is_new_arrival"
                                                    {{ $product->is_new_arrival == 1 ? 'checked' : '' }} id="newArrival">
                                                <label class="form-check-label" for="newArrival">
                                                    New Arrival
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row">
                                <div class="col-6">
                                    <div class="mb-4">
                                        <label for="" class="form-label">အရောင် / Color</label>
                                            <select class="js-example-basic-multiple form-control" name="english_colors[]" multiple="multiple">
                                                @foreach ($colors as $color)
                                                <option value="{{$color->english_name}}">
                                                    {{$color->english_name}}
                                                </option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-4">
                                        <label for="">Size</label>
                                        <select class="js-example-basic-multiple form-control" name="sizes[]" multiple="multiple">
                                            @foreach ($sizes as $size)
                                            <option value="{{$size->name}}">
                                                {{$size->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> --}}

                                <div class="row {{ $product->product_type == 2 ? 'd-none' : '' }}" id="single-product">
                                    <div class="col-6">
                                        <div class="mb-4">
                                            <label class="form-label">Stock</label>
                                            <input type="number" class="form-control" name="stock" autocomplete="off"
                                                value="{{ $product->stock }}">
                                             @error('stock')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-4">
                                            <label class="form-label">ဈေးနှုန်း</label>
                                            <input type="number" class="form-control" name="price" autocomplete="off"
                                                value="{{ $product->price }}">
                                             @error('price')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>





                                </div>
                                <div id="variation-container" class="{{ $product->product_type == 1 ? 'd-none' : '' }}">
                                    @foreach ($product->variations as $item)
                                        <div class="row">
                                            <input type="hidden" name="variation_ids[]" value="{{ $item->id }}">
                                            <div class="col-2">
                                                <div class="mb-4">
                                                    <label class="form-label">Variation</label>
                                                    <select name="variations[]" id="variation" class="form-select"
                                                        onchange="fetchTypes(event)">
                                                        <option hidden selected disabled>Select</option>
                                                        @foreach ($variations as $variation)
                                                            <option value="{{ $variation->id }}"
                                                                {{ $item->variation_id == $variation->id ? 'selected' : '' }}>
                                                                {{ $variation->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="mb-4">
                                                    <label class="form-label">Type</label>
                                                    <select name="types[]" id="type" class="form-select">
                                                        <option hidden selected disabled>Select</option>
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type->id }}"
                                                                {{ $item->variation_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-4">
                                                    <label for="" class="form-label">Variation Options</label>
                                                    <select class="js-example-basic-multiple form-control"
                                                        name="option_variation_ids[{{ $item->id }}][]"
                                                        multiple="multiple">
                                                        @if ($item->option_type_ids)
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ in_array($type->id, json_decode($item->option_type_ids)) ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}">
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                              <div class="col-2">
                                                <div class="mb-4">
                                                    <label class="form-label">Price</label>
                                                    {{-- <input type="number" class="form-control" name="prices[]"
                                                        autocomplete="off" value="{{ $item->price }}"> --}}
                                                    <select name="prices[{{ $item->id }}][]"
                                                        class="form-control prices" multiple="multiple">
                                                        @foreach (json_decode($item->price) as $price)
                                                            <option value="{{ $price }}" selected>
                                                                {{ $price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-2">
                                                <div class="mb-4">
                                                    <label class="form-label">Stock</label>
                                                    {{-- <input type="number" class="form-control" name="stocks[]"
                                                        autocomplete="off" value="{{ $item->stock }}"> --}}
                                                    <select name="stocks[{{ $item->id }}][]"
                                                        class="form-control stocks" multiple="multiple">
                                                        @foreach (json_decode($item->stock) as $stock)
                                                            <option value="{{ $stock }}" selected>
                                                                {{ $stock }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                             @if ($item->discount_price !=null)

                                             <div class="col-2">
                                                <div class="mb-4">
                                                    <label class="form-label">Discount Price</label>
                                                    {{-- <input type="number" class="form-control" name="prices[]"
                                                        autocomplete="off" value="{{ $item->price }}"> --}}
                                                    <select name="discounts[{{ $item->id }}][]"
                                                        class="form-control discounts" multiple="multiple">
                                                        @foreach (json_decode($item->discount_price) as $discount_price)
                                                            <option value="{{ $discount_price }}" selected>
                                                                {{ $discount_price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @elseif($item->discount_price==null)
                                            <div class="col-2">
                                                <div class="">
                                                    <div class="mb-4">
                                                        <label class="form-label">Discount Price</label>
                                                        <select name="discounts[{{ $item->id }}][]" class="form-control discounts" multiple="multiple">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif


                                            <div class="col-1">
                                                <div class="form-group">
                                                    <label for="colors">Color</label>
                                                    <input type="color" name="colors[{{ $item->id }}]"  id="colors"
                                                           value="{{ $item->color ?? '#000000' }}" class="form-control form-control-color" />
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="mb-4">
                                                    <button type="button" class="btn btn-danger rounded-circle"
                                                        onclick="deleteVariation(this)"><i
                                                            class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <button class="btn btn-primary mb-4" type="button" id="add-variation"
                                        onclick="addVariation()">+ Add</button>
                                </div>
                                <div class="mb-4">
                                    <label for="description" class="form-label">အကြောင်းအရာ / Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="8">{{ $product->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="images">Images</label>
                                    <div class="input-images" id="images"></div>
                                </div>

                                <div class="text-end submit-m-btn">
                                    <button type="button" class="submit-btn" onclick="submitProduct(this)">Product
                                        ပြုပြင်မည်</button>
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
    {!! JsValidator::formRequest('App\Http\Requests\UpdateProductRequest', '#product_create') !!}
    <script src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
    <script>
        ClassicEditor.defaultConfig = {
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|', 'heading',
                    '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                    '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                    '|', 'link', 'blockQuote', 'codeBlock',
                    '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent'
                ],
                shouldNotGroupWhenFull: false
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            },
            language: 'en'
        };
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800,
            width: '18em',
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        $.ajax({
            url: `/product-images/${`{{ $product->id }}`}`
        }).done(function(response) {
            if (response) {
                $('.input-images').imageUploader({
                    preloaded: response,
                    imagesInputName: 'images',
                    preloadedInputName: 'old',
                    maxSize: 2 * 1024 * 1024,
                    maxFiles: 10
                });
            }
        });

         document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category');
            const subCategorySelect = document.getElementById('sub_category');

            categorySelect.addEventListener('change', function () {
                const categoryId = this.value;
                subCategorySelect.innerHTML = '<option selected disabled>Loading...</option>';
                subCategorySelect.disabled = true;

                fetch(`/categories/${categoryId}/subcategories`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    subCategorySelect.innerHTML = '<option selected disabled>Sub Category ရွေးပါ</option>';
                    if (data.subcategories && data.subcategories.length > 0) {
                        data.subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subCategorySelect.appendChild(option);
                        });
                        subCategorySelect.disabled = false;
                    } else {
                        subCategorySelect.innerHTML = '<option selected disabled>No Sub Category Found</option>';
                    }
                })
                .catch(() => {
                    subCategorySelect.innerHTML = '<option selected disabled>Error loading</option>';
                });
            });
        });

        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                width: '100%',
                placeholder: "Select an Option",
                allowClear: true
            });
            $(".prices").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            })
             $(".discounts").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            })
            $(".stocks").select2({
                tags: true,
                tokenSeparators: [',', ' ']
            })
        });
        let variationContainer = document.getElementById('variation-container')
        let singleProduct = document.getElementById('single-product')
        let variationTemplate = (uniqueId) => {
            return `<div class="row">
                                        <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">Variation</label>
                                                <select name="variations[]" id="variation" class="form-select" onchange="fetchTypes(event)">
                                                <option hidden selected disabled>Select</option>
                                                    @foreach ($variations as $variation)
                                                        <option value="{{ $variation->id }}">
                                                            {{ $variation->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">Type</label>
                                                <select name="types[]" id="type" class="form-select">
                                                    <option hidden selected disabled>Select</option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type->id }}">
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">Color</label>
                                                <input type="color" name="colors[${uniqueId}]" class="form-control form-control-color" value="#ff0000" title="Choose color">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="mb-4">
                                                <label for="" class="form-label">Variation Options</label>
                                                    <select class="js-example-basic-multiple form-control" name="option_variation_ids[${uniqueId}][]" multiple="multiple">
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type->id }}">
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">ဈေးနှုန်း</label>
                                                <select name="prices[${uniqueId}][]" class="form-control prices" multiple="multiple">
                                                </select>
                                            </div>
                                        </div>

                                          <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">Discount</label>
                                                <select name="discounts[${uniqueId}][]" class="form-control discounts" multiple="multiple">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-4">
                                                <label class="form-label">Stock</label>
                                                <select name="stocks[${uniqueId}][]" class="form-control stocks" multiple="multiple">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="mb-4">
                                                <button type="button" class="btn btn-danger rounded-circle"
                                                onclick="deleteVariation(this)"><i class="ri-delete-bin-line"></i></button>
                                            </div>
                                        </div>
                                    </div>`
        }

        function formatResultData(data) {
            if (!data.id) return data.text;
            if (data.element.selected) return
            return data.text;
        };

        function changeProductType(select) {
            let type = select.value
            if (type == 2) {
                singleProduct.classList.add('d-none')
                variationContainer.classList.remove('d-none')
                variationContainer.insertAdjacentHTML('afterbegin', variationTemplate(Date.now() + Math.floor(Math
                    .random() * 1000000)))
                let optionSelect = $('.js-example-basic-multiple').select2({
                    width: '100%',
                    placeholder: "Select an Option",
                    allowClear: true
                });
                let priceSelect = $(".prices").select2({
                    maximumSelectionLength: 0,
                    tags: true,
                    templateResult: formatResultData,
                    tokenSeparators: [',', ' ']
                })
                let previousPrice = null
                allowDupliateTags(priceSelect, previousPrice)
                 let discountSelect = $(".discounts").select2({
                    maximumSelectionLength: 0,
                    tags: true,
                    templateResult: formatResultData,
                    tokenSeparators: [',', ' ']
                })
                let previousDiscount = null
                allowDupliateTags(discountSelect, previousDiscount)
                let stockSelect = $(".stocks").select2({
                    maximumSelectionLength: 0,
                    tags: true,
                    templateResult: formatResultData,
                    tokenSeparators: [',', ' ']
                })
                let previousStock = null
                allowDupliateTags(stockSelect, previousStock)
                optionSelect.on('change', e => {
                    let count = $(e.target).select2('data').length;
                    $(priceSelect).select2({
                        maximumSelectionLength: count,
                        tags: true,
                        tokenSeparators: [',', ' ']
                    })
                    $(stockSelect).select2({
                        maximumSelectionLength: count,
                        tags: true,
                        tokenSeparators: [',', ' ']
                    })
                })
            } else {
                singleProduct.classList.remove('d-none')
                variationContainer.classList.add('d-none')
                let addVarBtn = variationContainer.querySelector('#add-variation')
                variationContainer.innerHTML = ''
                variationContainer.insertAdjacentHTML('beforeend', addVarBtn.outerHTML)
            }
        }

        function addVariation() {
            let addVarBtn = variationContainer.querySelector('#add-variation')
            addVarBtn.remove()
            variationContainer.insertAdjacentHTML('beforeend', variationTemplate(Date.now() + Math.floor(Math.random() *
                1000000)))
            variationContainer.insertAdjacentHTML('beforeend', addVarBtn.outerHTML)
            let optionSelect = $('.js-example-basic-multiple').select2({
                width: '100%',
                placeholder: "Select an Option",
                allowClear: true
            });
            let priceSelect = $(".prices").select2({
                maximumSelectionLength: 0,
                tags: true,
                templateResult: formatResultData,
                tokenSeparators: [',', ' ']
            })
            let previousPrice = null
            allowDupliateTags(priceSelect, previousPrice)

            let discountSelect = $(".discounts").select2({
                maximumSelectionLength: 0,
                tags: true,
                templateResult: formatResultData,
                tokenSeparators: [',', ' ']
            })
            let previousDiscount = null
            allowDupliateTags(discountSelect, previousDiscount)
            let stockSelect = $(".stocks").select2({
                maximumSelectionLength: 0,
                tags: true,
                templateResult: formatResultData,
                tokenSeparators: [',', ' ']
            })
            let previousStock = null
            allowDupliateTags(stockSelect, previousStock)
            optionSelect.on('change', e => {
                let count = $(e.target).select2('data').length;
                $(priceSelect).select2({
                    maximumSelectionLength: count,
                    tags: true,
                    tokenSeparators: [',', ' ']
                })
                $(stockSelect).select2({
                    maximumSelectionLength: count,
                    tags: true,
                    tokenSeparators: [',', ' ']
                })
            })
        }

        function deleteVariation(ele) {
            ele.closest('.row').remove()
        }

        function allowDupliateTags(selectEle, previousTag) {
            $(selectEle).on("select2:select", function(e) {
                let currentTag = e.params.data.id;
                let selectedTags = $(selectEle).val();

                if (previousTag !== null && previousTag !== currentTag) {
                    let currentSelections = $(selectEle).val();

                    if (selectedTags.length > 1) {
                        selectedTags.splice(selectedTags.length - 2, 1);
                    }
                    $(selectEle).empty()

                    selectedTags.forEach(tag => {
                        $(selectEle).append($('<option></option>').attr("value", tag).text(tag));
                    });
                    console.log(selectEle);

                    $(selectEle).val(selectedTags).trigger('change');
                }

                previousTag = currentTag;
                $(selectEle).append('<option value="' + e.params.data.text + '">' + e.params.data
                    .text + '</option>');
            })
            $(selectEle).on("select2:unselect", function(e) {
                e.params.data.element.remove();
            });
        }

        function fetchTypes(event) {
            let typeSelect = event.target.closest('.row').querySelector('#type')
            typeSelect.innerHTML = ''
            let id = event.target.value;
            option = "<option selected hidden disabled>------- Loading -------</option>"
            typeSelect.innerHTML = option
            $.ajax({
                url: `/variations/${id}/types`,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                },
            }).done(function(res) {
                let types = res.types;
                if (types) {
                    typeSelect.innerHTML = ''
                    types.forEach(type => {
                        let option = document.createElement('option')
                        option.textContent = type.name
                        option.value = type.id
                        typeSelect.appendChild(option)
                    });
                    typeSelect.classList.remove('is-invalid');
                } else {
                    option = "<option selected hidden disabled>There's no type within this variation</option>"
                    typeSelect.classList.add('is-invalid');
                    typeSelect.innerHTML = option
                }
            })
        }

        function submitProduct(ele) {
            let prices = document.querySelectorAll('.prices')
            let stocks = document.querySelectorAll('.stocks')
            let allCorrect = false;
            prices.forEach((price, index) => {
                let priceCount = $(price).select2('data').length
                let stockCount = $(stocks[index]).select2('data').length
                let optionCount = $(price.closest('.row').querySelector('.js-example-basic-multiple')).select2(
                    'data').length
                if (optionCount == 0) {
                    Toast.fire({
                        icon: 'error',
                        title: "Select at least one variation option!"
                    })
                }
                if (priceCount < optionCount) {
                    Toast.fire({
                        icon: 'error',
                        title: "Please fill the prices and stocks to match options"
                    })
                }
                if (stockCount < optionCount) {
                    Toast.fire({
                        icon: 'error',
                        title: "Please fill the prices and stocks to match options"
                    })
                }
                if (priceCount == optionCount && stockCount == optionCount) {
                    allCorrect = true;
                } else {
                    allCorrect = false;
                }
                if (priceCount == 0 && optionCount == 0 && stockCount == 0) {
                    allCorrect = false;
                }
            })
            if (allCorrect) {
                document.getElementById('product_create').submit()
            }
            if (document.getElementById('product-type').value == 1) {
                document.getElementById('product_create').submit()
            }
        }
    </script>
@endsection
