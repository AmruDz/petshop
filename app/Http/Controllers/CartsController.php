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
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'sub_total' => 'required',
            'discount' => 'required',
            'total' => 'required',
        ])->validate();

        $dataProduct = Products::findOrFail($request->input('product_id'));
        $price = $dataProduct->price;
        $isDiscountActive = $dataProduct->is_discount_active;
        $isDiscountPercentage = $dataProduct->is_discount_percentage;
        $discount = $dataProduct->discount;

        $subtotal = $request->input('qty') * $price;

        $total = $this->calculated($subtotal, $discount, $isDiscountActive, $isDiscountPercentage);

        $validator['product_id'] = $dataProduct->id;
        $validator['price'] = $price;
        $validator['discount'] = $discount;
        $validator['sub_total'] = $subtotal;
        $validator['total'] = $total;

        $cart = Carts::create($validator);

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
    public function edit(Carts $carts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carts $carts)
    {
        //
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
