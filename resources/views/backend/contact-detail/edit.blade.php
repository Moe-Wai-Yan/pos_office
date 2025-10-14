@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header bg-transparent">
                <a href="{{ URL::previous() }}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Contact Detail ကိုပြုပြင်မည်</span>
                </a>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-9">
                        <form method="POST" action="{{route('contactDetail.update',$contactDetail->id)}}" id="delivery_create">
                            @csrf
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone',$contactDetail->phone) }}" placeholder="Eg. phone ....." required>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email',$contactDetail->email) }}" placeholder="Eg. email ....." required>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Address</label>
                                <textarea name="address" id="address" cols="30" rows="10" class="form-control" required>{{ old('address',$contactDetail->address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Facebook Url</label>
                                <input type="url" class="form-control" name="facebook_url" value="{{ old('facebook_url',$contactDetail->facebook_url) }}" placeholder="Enter your Facebook URL">
                            </div>
                             <div class="mb-3">
                                <label for="" class="form-label mb-3">Messenger Url</label>
                                <input type="url" class="form-control" name="messenger_url" value="{{ old('messenger_url',$contactDetail->messenger_url) }}" placeholder="Enter your Messenger URL">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Viber</label>
                                <input type="url" class="form-control" name="viber_url" value="{{ old('viber_url',$contactDetail->viber_url) }}" placeholder="Enter your Viber URL">
                            </div>
                             <div class="mb-3">
                                <label for="" class="form-label mb-3">Tik tok</label>
                                <input type="url" class="form-control" name="tiktok_url" value="{{ old('viber_url',$contactDetail->tiktok_url) }}" placeholder="Enter your Viber URL">
                            </div>
                            <div class="text-end submit-m-btn">
                                <button type="submit" class="submit-btn">ပြုပြင်မည်</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
