<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Categories::all();
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
        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
        ])->validate();

        $categories = Categories::create($validatedData);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $categories,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Categories::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $categories = Categories::findOrFail($id);

        $categories->update($data );

        return response()->json([
            'message' => 'Category updated successfully!',
            'data' => $categories,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categories = Categories::find($id);

        if (!$categories){
            return response()->json(['message' => 'Category not found!', Response::HTTP_NOT_FOUND]);
        }

        $categories->delete();

        return response()->json(['message' => 'Category deleted successfully!', Response::HTTP_OK]);
    }
}
