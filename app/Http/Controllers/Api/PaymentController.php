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

        // Generate a unique transaction ID
        $transactionId = 'TXN-' . strtoupper(uniqid());

        $payment = Payment::create([
            'store_id' => $request->store_id,
            'amount'   => $request->amount ?? null,
            'note'     => $request->note ?? null,
            'status'   => 'paid',
            'transaction_id' => $transactionId,
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

    // update note
    public function updateNote(Request $request, $id)
{
    $request->validate([
        'note' => 'nullable|string|max:500',
    ]);

    $payment = Payment::find($id);
    if (!$payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    $payment->note = $request->note;
    $payment->save();

    return response()->json(['message' => 'Note updated successfully', 'note' => $payment->note]);
}

}
