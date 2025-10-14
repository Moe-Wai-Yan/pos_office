@extends('main')

@section('content')
    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card my_card">
                <div class="card-header bg-transparent">
                    <a href="{{ route('versionSetting') }}"
                        class="card-title mb-0 d-inline-flex align-items-center create_title">
                        <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                        <span class="create_sub_title">Version Setting ကိုပြုပြင်မည်</span>
                    </a>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-9">

                            <form method="POST" action="{{ route('versionSetting.update', $version->id) }}"
                                id="version_update">
                                @csrf
                                <div class="mb-3">
                                    <label for="android_version" class="form-label mb-3">Android Version</label>
                                    <input type="text" class="form-control" name="android_version" id="android_version"
                                        value="{{ $version->android_version }}">
                                    @error('android_version')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="ios_version" class="form-label mb-3">IOS Version</label>
                                    <input type="text" class="form-control" name="ios_version" id="ios_version"
                                        value="{{ $version->ios_version }}">
                                    @error('ios_version')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="playstore_link" class="form-label mb-3">Playstore Link</label>
                                    <input type="text" class="form-control" name="playstore_link" id="playstore_link"
                                        value="{{ $version->playstore_link }}">
                                    @error('playstore_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="appstore_link" class="form-label mb-3">Appstore Link</label>
                                    <input type="text" class="form-control" name="appstore_link" id="appstore_link"
                                        value="{{ $version->appstore_link }}">
                                    @error('appstore_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="android_other_link" class="form-label mb-3">Android Other Link</label>
                                    <input type="text" class="form-control" name="android_other_link" id="android_other_link"
                                        value="{{ $version->android_other_link }}">
                                    @error('android_other_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="ios_other_link" class="form-label mb-3">IOS Other Link</label>
                                    <input type="text" class="form-control" name="ios_other_link" id="ios_other_link"
                                        value="{{ $version->ios_other_link }}">
                                    @error('ios_other_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="release_note" class="form-label mb-3">Release Note</label>
                                    <input type="text" class="form-control" name="release_note" id="release_note"
                                        value="{{ $version->release_note }}">
                                    @error('release_note')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="text-end submit-m-btn">
                                    <button type="submit" class="submit-btn">ပြင်ဆင်မှုများကိုသိမ်းမည်</button>
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
    </script>
@endsection
