<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    //api controller
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

    //web route
    public function indexMaster()
    {
        $category = Categories::orderBy('name', 'asc')->get();

        return view('', compact('category'));
    }
    public function createMaster()
    {
        return view('', compact('category'));
    }
    public function storeMaster(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $category = Categories::create($request->all());

        return redirect()->route('category')->with('success', 'Category created successfully');
    }
    public function editMaster()
    {
        return view('', compact('category'));
    }
    public function updateMaster(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => 'required',
        ])->validate();

        $category = Categories::findOrFail($id);

        if (!$category) {
            return redirect()->route('category')->with('error', 'Category not found');
        }

        $category->update($request->all());

        return redirect()->route('category')->with('success', 'Category updated successfully');
    }
    public function destroyMaster($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return redirect()->route('category')->with('error', 'Category not found');
        }

        $category->delete();

        return redirect()->route('category')->with('success', 'Category successfully deleted');
    }
}
