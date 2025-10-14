<?php

namespace App\Http\Controllers\API;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller
{
    public function subCategoryList(Request $request){
        $subCategories = SubCategory::with('category')
            ->when($request->category_id, function($query) use ($request) {
            return $query->where('category_id', $request->category_id);
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Sub Categories',
            'data' => SubCategoryResource::collection($subCategories)
        ])->setStatusCode(200, 'Sub Categories Retrieved Successfully');
    }
}
