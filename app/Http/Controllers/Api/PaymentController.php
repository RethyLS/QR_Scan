<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Store;

class PaymentController extends Controller
{
    // List payments
    public function index()
    {
        $payments = Payment::with('store')->get();
        return response()->json(['payments' => $payments]);
    }

    // Create payment and mark store as paid
    public function markPaid(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'amount'   => 'nullable|numeric',
            'note'     => 'nullable|string', // optional note
        ]);

        // create payment (without transaction_id)
        $payment = Payment::create([
            'store_id' => $request->store_id,
            'amount'   => $request->amount ?? null,
            'note'     => $request->note ?? null,
            'status'   => 'paid', // optional, if you have a status column
        ]);

        // update store status
        $store = Store::find($request->store_id);
        $store->update(['status' => 'paid']);

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'store'   => $store,
        ]);
    }
}
