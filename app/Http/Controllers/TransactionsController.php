<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function checkoutTransaction()
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
    public function calculatePendingTransactions()
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
    public function orderNumber($orderNumber)
    {
        if ($orderNumber < 10) {
            return '#' . '0' . '0' . '0' . '0' . '0' . $orderNumber;
        } elseif ($orderNumber >= 10) {
            return '#' . '0' . '0' . '0' . '0' . $orderNumber;
        } elseif ($orderNumber >= 100) {
            return '#' . '0' . '0' . '0' . $orderNumber;
        } elseif ($orderNumber >= 1000) {
            return '#' . '0' . '0' . $orderNumber;
        } elseif ($orderNumber >= 10000) {
            return '#' . '0' . $orderNumber;
        } else {
            return '#' . $orderNumber;
        }
    }
    public function formatToIDR($total)
    {
        return 'Rp' . ' ' . number_format($total, 0, ',', '.');
    }
    public function formatPercent($discount)
    {
        return $discount . ' ' . '%';
    }

    //api controller
    public function index()
    {
        $headerDate = Transactions::select(
            DB::raw('DATE(date) as date')
        )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        $result = [];

        try {
            foreach ($headerDate as $date) {
                $dateFormatted = Carbon::parse($date->date)->format('l, d F Y');

                $transactions = Transactions::with('cashier')->whereDate('date', $date->date)->orderBy('id', 'desc')->get();
                $total = $transactions->sum('total');
                $totalFormatted = $this->formatToIDR($total);
                $data = [
                    'date' => $dateFormatted,
                    'total' => $totalFormatted,
                    'data' => $transactions
                ];
                $result[] = $data;
            }

            return response()->json([
                'message' => 'Transactions retrieved successfully',
                'data' => $result,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying transactions data',
            ], 500);
        }
    }
    public function details($transaction_id)
    {
        $carts = Carts::with('transaction')->where('transaction_id', $transaction_id)->get();

        try {
            foreach ($carts as $format) {
                $format->price = $this->formatToIDR($format->price);
                $format->sub_total = $this->formatToIDR($format->sub_total);
                $format->total = $this->formatToIDR($format->total);
                if ($format->discount == 0) {
                    $format->discount;
                } else if ($format->discount < 100) {
                    $format->discount = $this->formatPercent($format->discount);
                } else{
                    $format->discount = $this->formatToIDR($format->discount);
                }
            }

            return response()->json([
                'details' => $carts,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying transactions data',
            ], 500);
        }
    }
    public function pending()
    {
        $pendingCart = Carts::with('product')->where('transaction_id', null)->get();
        $totalTransactions = Transactions::count();

        try {
            $orderNumber = $totalTransactions + 1;
            $toBePaid = $this->calculatePendingTransactions();

            return response()->json([
                'order_number' => $orderNumber = $this->orderNumber($orderNumber),
                'to_be_paid' => $toBePaid,
                'data' => $pendingCart,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while displaying pending transactions data',
            ], 500);
        }
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'customer' => 'required',
            'paid' => 'required',
        ])->validate();

        $calculated = $this->checkoutTransaction();

        $paid = $request->input('paid');

        $user = auth()->user();
        $cashierId = $user->id;

        try {
            if ($calculated['total'] > $paid) {
                return response()->json([
                    'message' => 'Cannot continue the transaction!',
                ], 400);
            } else {
                $transactionNumber = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . time();

                $transaction = Transactions::create([
                    'cashier_id' => $cashierId,
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

                $return = $paid - $calculated['total'];

                return response()->json([
                    'message' => 'Transaction successfully checked out!',
                    'return' => $return = $this->formatToIDR($return),
                    'data' => $transaction,
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while checkout transactions',
            ], 500);
        }
    }

    //web controller
    public function indexMaster()
    {
        $transaction = Transactions::orderBy('id', 'desc')->get();

        return view('', compact('transaction'));
    }
    public function detailsMaster($id)
    {
        $transaction = Transactions::with('cart')->findOrFail($id);

        return view('', compact('transaction'));
    }
    public function destroyMaster($id)
    {
        $transaction = Transactions::findOrFail($id);

        if (!$transaction) {
            return redirect()->route('')->with('error', 'Product not found');
        } else{
            $transaction->delete();

            return redirect()->route('')->with('success', 'Product delete successfully');
        }
    }
}
