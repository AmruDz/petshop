<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $headerDate = Transactions::select(
            DB::raw('DATE(date) as date')
        )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        $result = [];
        foreach ($headerDate as $date) {
            $dateFormatted = Carbon::parse($date->date)->format('l, d F Y');

            $transactions = Transactions::whereDate('date',  $date->date)->orderBy('id', 'desc')->get();
            $total = $transactions->sum('total');
            $totalFormatted = $this->formatToIDR($total);
            $data = [
                'date' => $dateFormatted,
                'total' => $totalFormatted,
                'data' => $transactions
            ];
            $result[] = $data;
        }

        return response()->json($result);
    }

    public function formatToIDR($total)
    {
        return 'Rp' . ' ' . number_format($total, 0, ',', '.');
    }

    public function formatPercent($discount)
    {
        return $discount . ' ' . '%';
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
        Validator::make($request->all(), [
            'cashier_id' => 'required',
            'customer' => 'required',
            'paid' => 'required',
        ])->validate();

        $calculated = $this->checkotTransaction();

        $paid = $request->input('paid');
        $return = $paid - $calculated['total'];

        if ($calculated['total'] > $paid) {
            return response()->json([
                'message' => 'Cannot continue the transaction!',
            ], 400);
        } else {
            $transactionNumber = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . time();

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
                'return' => $return = $this->formatToIDR($return),
                'data' => $transaction,
            ], 201);
        }
    }

    public function checkotTransaction()
    {
        $dataCart = Carts::whereNull('transaction_id')->get();

        $subtotal = 0;
        $discount = 0;
        $total = 0;

        foreach ($dataCart as $item) {
            $total += $item->total;
            $subtotal += $item->sub_total;
        }

        $discount = $subtotal - $total;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }
    public function calculateCart()
    {
        $dataCart = Carts::whereNull('transaction_id')->get();

        $subtotal = 0;
        $discount = 0;
        $total = 0;

        foreach ($dataCart as $item) {
            $total += $item->total;
            $subtotal += $item->sub_total;
        }

        $discount = $subtotal - $total;

        return [
            'subtotal' => $subtotal = $this->formatToIDR($subtotal),
            'discount' => $discount = $this->formatToIDR($discount),
            'total' => $total = $this->formatToIDR($total),
        ];
    }
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $data = Carts::where('transaction_id', null)->get();
        $toBePaid = $this->calculateCart();

        return response()->json([
            'to be paid' => $toBePaid,
            'data' => $data,
        ], 200);
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
