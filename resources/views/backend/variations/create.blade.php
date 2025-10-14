@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('variation') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Variation အသစ်ဖန်တီးမည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-9">
                            <form method="POST" action="{{ route('variation.store') }}" id="variation_create">
                                @csrf
                                <div class="mb-3">
                                    <label for="employeeName" class="form-label mb-3">အမည်</label>
                                    <input type="text" class="form-control" name="name" required
                                        placeholder="Eg. Color">
                                </div>
                                <div class="mb-3" id="type-container">
                                    <label class="form-label mb-3">Types</label>
                                    <div class="d-flex gap-3">
                                        <input type="text" class="form-control" name="types[]" required
                                            placeholder="Eg. Black">
                                        {{-- <button class="btn btn-sm btn-danger" type="button"
                                            onclick="deleteType(this)"><i class="ri-delete-bin-5-line"></i></button> --}}
                                    </div>
                                </div>
                                <button class="btn btn-sm w-100 btn-info mt-3" type="button" id="add-type"
                                    onclick="addTypes()">+ Add</button>
                                <div class="text-end submit-m-btn">
                                    <button type="submit" class="submit-btn">Variation အသစ်ပြုလုပ်မည် </button>
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
    {!! JsValidator::formRequest('App\Http\Requests\StoreVariationRequest', '#variation_create') !!}
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
