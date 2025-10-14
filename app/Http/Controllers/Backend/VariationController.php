<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVariationRequest;
use App\Http\Requests\UpdateVariationRequest;
use App\Models\ProductVariation;
use App\Models\Variation;
use App\Models\VariationType;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    /**
     * product listing view
     *
     * @return void
     */
    public function index()
    {
        return view('backend.variations.index');
    }

    /**
     * Create Form
     *
     * @return void
     */
    public function create()
    {
        return view('backend.variations.create');
    }

    /**
     * Store Variation
     *
     * @param StoreVariationRequest $request
     * @return void
     */
    public function store(StoreVariationRequest $request)
    {
        $variation = new Variation();
        $variation->name = $request->name;
        $variation->save();
        if ($request->types) {
            foreach ($request->types as $name) {
                $type = new VariationType();
                $type->variation_id = $variation->id;
                $type->name = $name;
                $type->save();
            }
        }

        return redirect()->route('variation')->with('created', 'Variation created Successfully');
    }

    /**
     * Variation Edit
     *
     * @param [type] $id
     * @return void
     */
    public function edit(Variation $variation)
    {
        return view('backend.variations.edit', compact('variation'));
    }

    /**
     * Variation Update
     *
     * @param Reqeuest $request
     * @param [type] $id
     * @return void
     */
    public function update(UpdateVariationRequest $request, Variation $variation)
    {
        $variation->name = $request->name;

        if ($request->types && is_array($request->types)) {
            // Collect existing type IDs from the request
            $existingTypeIds = $request->type_ids ?? [];
            $newTypes = []; // To track newly created types

            foreach ($request->types as $key => $name) {
                $type = null;

                // Check if a type ID exists and corresponds to a type in the database
                if (isset($existingTypeIds[$key])) {
                    $type = VariationType::where('id', $existingTypeIds[$key])
                        ->where('variation_id', $variation->id)
                        ->first();
                }

                if (!$type) {
                    // Create a new VariationType if not found
                    $type = new VariationType();
                    $type->variation_id = $variation->id;
                }

                // Update the type's name
                $type->name = $name;
                $type->save();

                // Collect IDs for new or updated types
                $newTypes[] = $type->id;
            }

            // Determine types to delete (not in the updated or newly created list)
            $typesToDelete = VariationType::where('variation_id', $variation->id)
                ->whereNotIn('id', $newTypes)
                ->get();

            foreach ($typesToDelete as $type) {
                // Update related ProductVariations before deletion
                ProductVariation::where('variation_type_id', $type->id)
                    ->update(['variation_type_id' => null]);
            }

            // Delete the unused variation types
            VariationType::whereIn('id', $typesToDelete->pluck('id'))->delete();
        }

        // Update the variation itself
        $variation->save();

        return redirect()->route('variation')->with('updated', 'Variation Updated Successfully');
    }


    /**
     * delete Variation
     *
     * @return void
     */
    public function destroy(Variation $variation)
    {
        $variations = ProductVariation::where('variation_id', $variation->id)->get();
        foreach($variations as $variation) {
            $variation->variation_id = null;
            $variation->variation_type_id = null;
            $variation->update();
        }

        $types = VariationType::where('variation_id', $variation->id)->get();
        foreach($types as $type) {
            $type->delete();
        }
        $variation->delete();


        return 'success';
    }

    /**
     * ServerSide
     *
     * @return void
     */
    public function serverSide()
    {
        $variation = Variation::orderBy('id', 'desc')->get();
        return datatables($variation)
            ->addColumn('name', function ($each) {
                return $each->name;
            })
            ->addColumn('types', function ($each) {
                $typeNames = implode(", ", $each->types->pluck('name')->toArray());
                return $typeNames;
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('variation.edit', $each->id) . '" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';
                $delete_icon = '<a href="#" class="btn btn-sm btn-danger delete_btn" data-id="' . $each->id . '"><i class="mdi mdi-trash-can-outline btn_icon_size"></i></a>';

                return '<div class="action_icon">' . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['name', 'types', 'action'])
            ->toJson();
    }

    public function getTypes(Variation $variation)
    {
        return response()->json([
            'types' => $variation->types
        ]);
    }
}
