<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Products::orderBy('is_discount_active', 'desc')->get();

            foreach ($products as $change) {
                $change->price = $this->formatToIDR($change->price);
                if ($change->is_discount_active == 1) {
                    if ($change->is_discount_percentage == 1) {
                        $change->discount = $this->discPercent($change->discount);
                    } else {
                        $change->discount = $this->discIDR($change->discount);
                    }
                } else {
                    $change->discount;
                }
            }

            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying product data',
            ], 500);
        }
    }
    public function show($id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found!',
            ], 404);
        }

        try {
            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating product data',
            ], 500);
        }
    }
    public function store(Request $request)
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
            Storage::disk('products')->putFileAs('', $file, $fileName);
            $validator['image'] = $fileName;
        }

        try {
            $product = Products::create($validator);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating a product',
            ], 500);
        }
    }
    public function update(Request $request, $id)
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

        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found!',
            ], 404);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('products')->putFileAs('', $file, $fileName);
            $validator['image'] = $fileName;

            if ($product->image) {
                Storage::disk('products')->delete($product->image);
            }
        }

        try {
            $product->update($validator);

            return response()->json([
                'message' => 'Product updated successfully!',
                'data' => $product,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating product data',
            ], 500);
        }
    }
    public function destroy(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found!',
            ], 404);
        }

        if ($product->image) {
            Storage::disk('products')->delete($product->image);
        }

        try {
            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while deleting a product',
            ], 500);
        }
    }
    public function discPercent($discount)
    {
        return 'Disc' . ' ' . $discount . ' ' . '%';
    }
    public function discIDR($discount)
    {
        return 'Disc' . ' ' . 'Rp' . ' ' . number_format($discount, 0, ',', '.');
    }
    public function formatToIDR($price)
    {
        return 'Rp' . ' ' . number_format($price, 0, ',', '.');
    }
}
