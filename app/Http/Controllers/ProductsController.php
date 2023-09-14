<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Products::all();

        foreach ($data as $product) {
            $product->image = Storage::disk('products')->url("image/{$product->image}");
        }
        return response()->json($data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

    }


    public function store(Request $request):RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'unit_id' => 'required',
            'name' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,jfif|max:2048',
            'qty' => 'required',
            'price' => 'required',
            'is_discount_active' => 'boolean',
            'is_discount_percentage' => 'boolean',
            'discount' => 'required',
        ])->validate();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('products')->putFileAs($fileName, File::get($file));
        }

        $product = Products::create($validator);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Products::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'category_id' => 'required',
            'unit_id' => 'required',
            'name' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,jfif|max:2048',
            'qty' => 'required',
            'price' => 'required',
            'is_discount_active' => 'boolean',
            'is_discount_percentage' => 'boolean',
            'discount' => 'required',
        ]);
        ;

        $product = Products::findOrFail($id);

        if ($request->hasFile('image')) {
            $newFile = $request->file('image');
            $newFileName = uniqid() . '.' . $newFile->getClientOriginalExtension();

            $newFile->storeAs('public/image/', $newFileName);

            $validatedData['image'] = $newFileName;

            if ($product->image) {
                Storage::delete('public/image/' . $product->image);
            }

        }

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully!',
            'data' => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        if ($product->image) {
            Storage::delete('public/image/' . $product->image);
        }

        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
