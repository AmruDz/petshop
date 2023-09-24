<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        try {
            $categories = Categories::all();

            return response()->json([
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying category data',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid category data',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $category = Categories::create($request->all());

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating a category',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid category data',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $category = Categories::findOrFail($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found!',
                ], 404);
            }

            $category->update($request->all());

            return response()->json([
                'message' => 'Category updated successfully!',
                'data' => $category,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating a category',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found!',
            ], 404);
        }

        try {
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while deleting a category',
            ], 500);
        }
    }
}
