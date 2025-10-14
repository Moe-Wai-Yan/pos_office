@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('post') }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Post အသစ်ဖန်တီးမည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-9">
                            <form method="POST" action="{{ route('post.store') }}" id="post_create"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="description" class="form-label mb-3">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="10" class="form-control"></textarea>
                                    @error('description')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mb-3">Post Images</label>
                                    <div class="images-container">
                                    </div>
                                    <button class="btn btn-dark" type="button" onclick="addNewImages()">Add
                                        Image</button>
                                </div>
                                <div class="text-end submit-m-btn">
                                    <button type="submit" class="submit-btn">Post အသစ်ပြုလုပ်မည် </button>
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
    {!! JsValidator::formRequest('App\Http\Requests\StoreCategoryRequest', '#post_create') !!}
    <script src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
    <script>
        let template =
            `<div class="position-relative w-100 image-parent">
                                       <div class="upload mb-5">
                                            <div class="preview_img">
                                                <img src="{{ asset(config('app.companyInfo.logo')) }}" width=150 height=150
                                                        alt="">
                                            </div>
                                            <div class="round">
                                                <input type="file" name="images[]" onchange="updateImage(this)">
                                                <i class="ri-camera-fill" style="color: #fff;"></i>
                                            </div>
                                            </div>
                                        <button type="button" class="btn btn-danger delete-image" onclick="deleteImage(this)">Delete</button>
                                        <textarea name="contents[]" cols="30" rows="3" class="form-control mb-3" placeholder="Enter your image description"></textarea>
                </div>`

        let imageContainer = document.querySelector('.images-container')

        function addNewImages() {
            imageContainer.insertAdjacentHTML('beforeend', template)
        }

        function updateImage(ele) {
            let file_length = ele.files.length;
            let previewImage = $(ele.closest('.upload').querySelector('.preview_img'))
            console.log(previewImage);
            if (file_length > 0) {
                previewImage.html('');
                for (i = 0; i < file_length; i++) {
                    previewImage.html('');
                    previewImage.append(
                        `<img src="${URL.createObjectURL(ele.files[i])}" width=150 height =150/>`)
                }
            } else {
                previewImage.html(
                    `<img src="{{ asset(config('app.companyInfo.logo')) }}" width=150 height=150 alt="">`);
            }
        }

        function deleteImage(ele) {
            ele.closest('.image-parent').remove()
        }
    </script>
@endsection
