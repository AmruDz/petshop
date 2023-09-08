<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Products::all();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        // $products = Products::create([
        //     'category_id' => $validatedData['category_id'],
        //     'unit_id' => $validatedData['unit_id'],
        //     'name' => $validatedData['name'],
        //     'image' => $validatedData['image'],
        //     'qty' => $validatedData['qty'],
        //     'price' => $validatedData['price'],
        //     'is_discount_active' => $validatedData['is_discount_active'],
        //     'is_discount_percentage' => $validatedData['is_discount_percentage'],
        //     'discount' => $validatedData['discount'],
        // ]);

        $file = $request->file('image');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/image/', $fileName);
        $validatedData['image'] = $fileName;

        $product = Products::create($validatedData);

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
