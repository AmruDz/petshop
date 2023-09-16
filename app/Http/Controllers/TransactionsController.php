<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Transactions::all();
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
    public function store(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'cashier_id' => 'required',
            'customer' => 'required',
        ])->validate();

        $transactionNumber = uniqid();

        $calculated = $this->calculateCart();

        $transaction = Transactions::create([
            'cashier_id' => $request->input('cashier_id'),
            'date' => Carbon::now(),
            'transaction_number' => $transactionNumber,
            'customer' => $request->input('customer'),
            'sub_total' => $calculated['subtotal'],
            'discount' => $calculated['discount'],
            'total' => $calculated['total'],
        ]);

        $cartItems = Carts::where('transaction_id', null)->get();

        foreach ($cartItems as $cartItem) {
            $product = Products::find($cartItem->product_id);

            if ($product->qty >= $cartItem->qty) {
                $product->qty -= $cartItem->qty;
                $product->save();
            } else {
                return response()->json([
                    'message' => 'Stock is not sufficient for one or more products in the cart.',
                ], 400);
            }
        }

        Carts::where('transaction_id', null)->update(['transaction_id' => $transaction->id]);

        return response()->json([
            'message' => 'Transaction successfully checked out!',
            'data' => $transaction,
        ], 201);
    }

    public function calculateCart()
    {
        $dataCart = Carts::where('transaction_id', null)->get();

        $subtotal = 0;
        $discount = 0;
        $total = 0;

        foreach ($dataCart as $item) {
            $subtotal += $item->sub_total;
            $discount += $item->discount;
        }

        $total = $subtotal - $discount;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Transactions::findOrfail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transactions $transactions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transactions $transactions)
    {
        //
    }
}
