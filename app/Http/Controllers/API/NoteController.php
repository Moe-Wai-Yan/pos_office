<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index(){
       $notes=Note::all();
       return response()->json([
        'success'=>true,
        'message'=>'Note List',
        'status'=>200,
        'data'=>NoteResource::collection($notes)
       ],200);
    }
}
