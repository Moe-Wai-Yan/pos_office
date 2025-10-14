@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header bg-transparent">
                <a href="{{route('deliveryfee')}}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Note အသစ်ဖန်တီးမည်</span>
                </a>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-9">
                        <form method="POST" action="{{route('note.store')}}" id="note_create">
                            @csrf
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Note</label>
                               <textarea name="note" id="" cols="30" rows="10" class="form-control" placeholder="အသစ်ဖန်တီးမည်"></textarea>
                            </div>

                            <div class="text-end submit-m-btn">
                                <button type="submit" class="submit-btn">အသစ်ပြုလုပ်မည်</button>
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
    {!! JsValidator::formRequest('App\Http\Requests\NoteRequest', '#note_create') !!}
@endsection
