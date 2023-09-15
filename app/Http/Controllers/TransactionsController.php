<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Transactions;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        //
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
