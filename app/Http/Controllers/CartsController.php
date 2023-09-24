<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartsController extends Controller
{
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'product_id' => 'required',
            'qty' => 'required',
        ])->validate();

        $product_id = $request->input('product_id');
        $qty = $request->input('qty');

        $dataProduct = Products::findOrFail($product_id);
        $price = $dataProduct->price;
        $isDiscountActive = $dataProduct->is_discount_active;
        $isDiscountPercentage = $dataProduct->is_discount_percentage;
        $discount = $dataProduct->discount;
        $subtotal = $qty * $price;
        $total = $this->calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage);

        try {
            if ($dataProduct->qty >= $qty) {
                $cart = Carts::create([
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'price' => $price,
                    'sub_total' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                return response()->json([
                    'message' => 'The product has been added to the cart!',
                    'data' => $cart,
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Stock is not sufficient for one or more products in the cart.',
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while adding product to cart',
            ], 500);
        }
    }
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'qty' => 'required',
            'product_id' => 'required',
        ])->validate();

        $qty = $request->input('qty');
        $product_id = $request->input('product_id');

        $dataProduct = Products::findOrFail($product_id);
        $price = $dataProduct->price;
        $isDiscountActive = $dataProduct->is_discount_active;
        $isDiscountPercentage = $dataProduct->is_discount_percentage;
        $discount = $dataProduct->discount;
        $subtotal = $qty * $price;
        $total = $this->calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage);

        try {
            if ($dataProduct->qty >= $qty) {
                $cart = Carts::where('product_id', $product_id)
                    ->whereNull('transaction_id')
                    ->firstOrFail();

                $cart->update([
                    'qty' => $qty,
                    'price' => $price,
                    'sub_total' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                return response()->json([
                    'message' => 'The cart has been updated!',
                    'data' => $cart,
                ], 201);
            } else {
                return response()->json([
                    'message' => 'Stock is not sufficient for one or more products in the cart.',
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating cart data',
            ], 500);
        }
    }
    public function destroy(Request $request)
    {
        Validator::make($request->all(), [
            'product_id' => 'required',
            'qty' => 'required',
        ])->validate();

        $product_id = $request->input('product_id');
        $qty = $request->input('qty');

        try {
            if ($qty == 0) {
                $cart = Carts::where('product_id', $product_id)->whereNull('transaction_id')->get();

                if ($cart->isEmpty()) {
                    return response()->json([
                        'message' => 'Cart item not found!',
                    ], 404);
                }
                $cart->each(function ($cart) {
                    $cart->delete();
                });
                return response()->json([
                    'message' => 'Product has been removed from the cart!',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Failed to remove product from cart!',
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while removing the item from the cart.',
            ], 500);
        }
    }
    public function calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage)
    {
        if ($isDiscountActive) {
            if ($isDiscountPercentage) {
                return $subtotal - ($subtotal * $discount / 100);
            } else {
                return $subtotal - $discount;
            }
        } else {
            return $subtotal;
        }
    }
}
