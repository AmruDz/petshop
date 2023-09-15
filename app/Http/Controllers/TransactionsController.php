<?php

namespace App\Http\Controllers;

use App\Models\Carts;
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
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'cashier_id' => 'required',
            'customer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $transaction_number = uniqid();

        $calculated = $this->calculateCart();

        $transaction = Transactions::create([
            'cashier_id' => $request->input('cashier_id'),
            'date' => Carbon::now(),
            'transaction_number' => $transaction_number,
            'customer' => $request->input('customer'),
            'sub_total' => $calculated['subtotal'],
            'discount' => $calculated['discount'],
            'total' => $calculated['total'],
        ]);

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
            $subtotal += $item->subtotal;
            $discount += $item->discount;
        }

        $total = $subtotal - $discount;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    public function checkout(Request $request)
    {
        // Simpan data transaksi ke tabel 'transactions'
        $transaction = new Transactions;
        $transaction->user_id = $request->user()->id; // Sesuaikan dengan hubungan antara transaksi dan pengguna Anda.
        // Setel atribut lainnya sesuai dengan data transaksi.
        $transaction->save();

        // Dapatkan ID transaksi yang baru saja dibuat
        $newTransactionId = $transaction->id;

        // Perbarui 'transaction_id' di tabel 'carts' yang memiliki 'transaction_id' NULL dengan ID transaksi yang baru
        Carts::where('user_id', $request->user()->id)
            ->whereNull('transaction_id')
            ->update(['transaction_id' => $newTransactionId]);

        // Anda dapat menambahkan logika lainnya di sini, seperti mengirim email konfirmasi, dll.

        // Kemudian, Anda dapat memberikan respons bahwa transaksi telah berhasil.
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
