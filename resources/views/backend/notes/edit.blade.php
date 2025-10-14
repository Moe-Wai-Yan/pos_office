@extends('main')

@section('content')
<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card my_card">
            <div class="card-header bg-transparent">
                <a href="{{route('note')}}" class="card-title mb-0 d-inline-flex align-items-center create_title">
                    <i class=" ri-arrow-left-s-line mr-3 primary-icon"></i>
                    <span class="create_sub_title">Edit Note</span>
                </a>
            </div><!-- end card header -->
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-9">
                        <form method="POST" action="{{route('note.update',$note->id)}}" id="note_create">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="" class="form-label mb-3">Note</label>
                               <textarea name="note" id="" cols="30" rows="10" class="form-control" placeholder="အသစ်ဖန်တီးမည်">{{ $note->note }}</textarea>
                            </div>

                            <div class="text-end submit-m-btn">
                                <button type="submit" class="submit-btn">Update</button>
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
