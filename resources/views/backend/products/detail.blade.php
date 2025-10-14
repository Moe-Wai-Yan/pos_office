@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                    <a href="{{ route('product') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Product အချက်အလက်</span>
                    </a>
                    <a class="primary_button" href="{{ route('product.edit', $product->id) }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-square-edit-outline btn_icon_size primary-icon mr-2"></i>
                            <span class="button_content">Product ကို ပြုပြင်မည်</span>
                        </div>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-12">
                            <table class="table table-bordered" style="width: 100%">
                                <tbody>
                                    <tr>
                                        <th width="30%">အမည်</th>
                                        <td>{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">အမှတ်တံဆိပ် / Brand</th>
                                        <td>{{ $product->brand->name ?? '---' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">အမျိူးအစား / Category</th>
                                        <td>{{ $product->category->name ?? '---' }}</td>
                                    </tr>

                                    <tr>
                                        <th width="30%">Sub Category</th>
                                        <td>{{ $product->subCategory->name ?? '---' }}</td>
                                    </tr>

                                    @if ($product->is_new_arrival)
                                     <tr>
                                        <th width="30%">Arrival</th>
                                        <td>New Arrival</td>
                                    </tr>
                                    @endif

                                    @if ($product->variations && $product->variations->count())
                                        <div class="mb-3">
                                            <label><strong>Colors:</strong></label>
                                            <div class="d-flex gap-2">
                                                @foreach ($product->variations as $variation)
                                                    @if ($variation->color)
                                                        <div style="width: 30px; height: 30px; background-color: {{ $variation->color }}; border: 1px solid #000; border-radius: 5px;" title="{{ $variation->color }}"></div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif


                                     <tr>
                                        <th width="30%">Discount Price</th>
                                        <td>{{ number_format($product->discount_price) ?? '---' }}MMK</td>
                                    </tr>
                                    {{-- @if (count($product->english_colors))
                                <tr>
                                    <th width="30%">အရောင် / Colors ( English )</th>
                                    <td>
                                    @foreach ($product->english_colors as $item)
                                        <div class="bg-light text-dark shadow-sm px-3 py-1 d-inline-block rounded mr-3">{{ $item }}</div>
                                    @endforeach
                                    </td>
                                </tr>
                                @endif
                                @if (count($product->myanmar_colors))
                                <tr>
                                    <th width="30%">အရောင် / Colors ( Myanmar )</th>
                                    <td>
                                    @foreach ($product->myanmar_colors as $item)
                                        <div class="bg-light text-dark shadow-sm px-3 py-1 d-inline-block rounded mr-3">{{ $item }}</div>
                                    @endforeach
                                    </td>
                                </tr>
                                @endif
                                @if (count($product->sizes))
                                <tr>
                                    <th width="30%">Size</th>
                                    <td>
                                    @foreach ($product->sizes as $item)
                                        <div class="bg-light text-dark shadow-sm px-3 py-1 d-inline-block rounded mr-3">{{ $item }}</div>
                                    @endforeach
                                    </td>
                                </tr>
                                @endif --}}
                                    @if ($product->product_type == 1)
                                        <tr>
                                            <th width="30%">
                                                <p class="mb-0 ">စျေးနှုန်း</p>
                                            </th>
                                            <td class="">{{ number_format($product->price) }} MMK</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">
                                                <p class="mb-0 ">လက်ကျန်</p>
                                            </th>
                                            <td class="">
                                                <div
                                                    class="badge fs-6 rounded-pill {{ $product->stock ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                    {{ $product->stock > 0 ? $product->stock : 'out of stock' }}</div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if ($product->product_type == 2)
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header bg-white">Variations</div>
                                    <div class="card-body">
                                        <table class="table table-bordered" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Variation</th>
                                                    <th>Type</th>
                                                    <th>Additonal Types</th>
                                                    <th>Price</th>
                                                    <th>Discount</th>
                                                    <th>Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->variations as $variation)
                                                    <tr>
                                                        <td>
                                                            {{ $variation->variation->name ?? '---' }}
                                                        </td>
                                                        <td>
                                                            {{ $variation->type->name ?? '---' }}
                                                        </td>
                                                        <td>
                                                            @if ($variation->option_type_ids)
                                                                {{ implode(
                                                                    ', ',
                                                                    App\Models\VariationType::whereIn('id', json_decode($variation->option_type_ids))->orderBy('id','desc')->pluck('name')->toArray(),
                                                                ) }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @foreach (json_decode($variation->price)?? [] as $price)
                                                                {{ $price }}&nbsp;
                                                            @endforeach
                                                        </td>
                                                         <td>
                                                            @foreach (json_decode($variation->discount_price) ?? [] as $discount_price)
                                                                {{ $discount_price }}&nbsp;
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @foreach (json_decode($variation->stock)?? [] as $stock)
                                                                {{ $stock }}&nbsp;
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <p class="mb-0">အကြောင်းအရာ / Description</p>
                                </div>
                                <div class="card-body">
                                    <p>{{ $product->description ?? '---' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <p class="mb-0">Images</p>
                                </div>
                                <div class="card-body d-flex flex-wrap">
                                    @foreach ($product->images as $img)
                                        <div class="mx-2 rounded">
                                            <img src="{{ $img->path }}" alt="{{ $product->name }}" class="rounded"
                                                srcset="" style="width: 100px; height: 100px">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
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
        })
    </script>
@endsection
