<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\NoteRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index(){

        return view('backend.notes.index');
    }

    public function create(){
        return view('backend.notes.create');
    }

    public function store(NoteRequest $request){
        $validate=$request->validated();
        $note=new Note();
        $note->note=$validate['note'];
        $note->save();
        return redirect()->route('note');
    }

    public function edit(Note $note){
        return view('backend.notes.edit',compact('note'));
    }

    public function update(Note $note,NoteRequest $request){

        $validate=$request->validated();
        $note->note=$validate['note'];
        $note->save();
        return redirect()->route('note');
    }

    public function destroy(Note $note){
        $note->delete();
        return redirect()->route('note');
    }

     public function serverSide()
    {
        $data = Note::orderBy('id', 'desc')->get();
        return datatables($data)
            ->addColumn('note', function ($each) {
                return $each->note ?? '-----';
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('note.edit', $each->id) . '" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">' . $edit_icon . '</div>';
            })
            ->rawColumns(['note','action'])
            ->toJson();
    }
}
