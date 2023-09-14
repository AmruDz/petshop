<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Carts::all();
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_id' => 'null',
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'subtotal' => 'required',
            'discount' => 'required',
            'total' => 'required',
        ]);

        $total = 0;
        foreach ($validatedData as $calculated) {
            $total += $calculated->subtotal;
        }
        $validatedData['total'] = $total;

        $cart = Carts::create($validatedData);

        return response()->json([
            'message' => 'Cart created successfully!',
            'data' => $cart,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $data = Carts::findOrFail($id);
         // return response()->json($data);
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
    public function destroy(Carts $carts)
    {
        //
    }
}
