<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($transaction_id)
    {
        $data = Carts::where('transaction_id', $transaction_id)->get();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request): JsonResponse
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
        $subtotal= $qty * $price;
        $total = $this->calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage);

            if ($dataProduct->qty >= $qty) {
                $dataProduct->qty -= $qty;
            } else {
                return response()->json([
                    'message' => 'Stock is not sufficient for one or more products in the cart.',
                ], 400);
            }

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

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $data = Carts::whereNull('transaction_id')->get();
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'qty' => 'required',
        ])->validate();

        $qty = $request->input('qty');

        $cart = Carts::findOrFail($id);
        $product_id = $cart->product_id;

        $dataProduct = Products::findOrFail($product_id);
        $price = $dataProduct->price;
        $isDiscountActive = $dataProduct->is_discount_active;
        $isDiscountPercentage = $dataProduct->is_discount_percentage;
        $discount = $dataProduct->discount;
        $subtotal = $qty * $price;
        $total = $this->calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage);

        if ($dataProduct->qty >= $qty) {
            $dataProduct->qty -= $qty;
        } else {
            return response()->json([
                'message' => 'Stock is not sufficient for one or more products in the cart.',
            ], 400);
        }
        
        $cart->update([
            'product_id' => $product_id,
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $carts = Carts::findOrFail($id);

        if (!$carts){
            return response()->json(['message' => 'The product on the cart was not found', Response::HTTP_NOT_FOUND]);
        }

        $carts->delete();

        return response()->json(['message' => 'The product has been removed from the cart!', Response::HTTP_OK]);
    }
}
