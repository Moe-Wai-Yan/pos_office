<?php

namespace App\Http\Controllers\Backend;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;

class SubCategoryController extends Controller
{
    public function index(){
        return view('backend.sub-category.index');
    }

    public function create(){
        $categories=Category::orderBy('id','desc')->get();
        return view('backend.sub-category.create', compact('categories'));
    }

    public function store(StoreSubCategoryRequest $request){
        $validatedData=$request->validated();
        SubCategory::create($validatedData);
        return redirect()->route('subcategory')->with('created', 'Sub Category created successfully.');
    }

    public function edit(SubCategory $subCategory){
        $categories=Category::orderBy('id','desc')->get();
        return view('backend.sub-category.edit', compact('subCategory', 'categories'));
    }

    public function update(StoreSubCategoryRequest $request , SubCategory $subCategory){
        $validatedData=$request->validated();
        $subCategory->update($validatedData);
        return redirect()->route('subcategory')->with('updated', 'Sub Category updated successfully.');
    }

    public function destroy(SubCategory $subCategory){
        $subCategory->delete();
        return 'success';
    }

     public function serverSide()
    {
        $subCategories = SubCategory::with('category')->orderBy('id','desc')->get();
        return datatables($subCategories)
        ->addColumn('category',function($each){
            return $each->category ? $each->category->name : '---';
        })
        ->addColumn('action', function ($each) {
            $edit_icon = '<a href="'.route('subcategory.edit', $each->id).'" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
            $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="'.$each->id.'"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';

            return '<div class="action_icon">'.$edit_icon. $delete_icon .'</div>';
        })
        ->rawColumns([ 'category','action'])
        ->toJson();
    }
}
