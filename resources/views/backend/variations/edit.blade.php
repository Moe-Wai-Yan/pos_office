@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('variation') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Variation ပြုပြင်မည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-9">
                            <form method="POST" action="{{ route('variation.update', $variation->id) }}"
                                id="variation_edit">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="employeeName" class="form-label mb-3">အမည်</label>
                                    <input type="text" class="form-control" name="name" required
                                        placeholder="Eg. Color" value="{{ $variation->name }}">
                                </div>
                                <div class="mb-3" id="type-container">
                                    <label class="form-label mb-3">Types</label>
                                    @foreach ($variation->types as $type)
                                        <input type="hidden" name="type_ids[]" value="{{ $type->id }}">
                                        <div class="d-flex gap-3 mt-3">
                                            <input type="text" class="form-control" name="types[]" required
                                                placeholder="Eg. Black" value="{{ $type->name }}">
                                            @if (!$loop->first)
                                                <button class="btn btn-sm btn-danger" type="button"
                                                    onclick="deleteType(this)">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-sm w-100 btn-info mt-3" type="button" id="add-type"
                                    onclick="addTypes()">+ Add</button>
                                <div class="text-end submit-m-btn">
                                    <button type="submit" class="submit-btn">Variation ပြုပြင်မည် </button>
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
    {!! JsValidator::formRequest('App\Http\Requests\UpdateVariationRequest', '#variation_edit') !!}
    <script>
        let typeContainer = document.getElementById('type-container')
        let typeTemplate = `<div class="d-flex gap-3 mt-3">
                                        <input type="text" class="form-control" name="types[]">
                                        <button class="btn btn-sm btn-danger" type="button"
                                            onclick="deleteType(this)"><i class="ri-delete-bin-5-line"></i></button>
                                    </div>`

        function addTypes() {
            typeContainer.insertAdjacentHTML('beforeend', typeTemplate)
        }

        function deleteType(ele) {
            ele.closest('.d-flex').remove()
        }
    </script>
@endsection
