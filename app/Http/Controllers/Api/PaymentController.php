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
        'store_id'   => 'required|exists:stores,id',
        'amount'     => 'nullable|numeric',
        'note'       => 'nullable|string',
        'created_at' => 'nullable|date', // selected date
    ]);

    $transactionId = 'TXN-' . strtoupper(uniqid());
    $createdAt = $request->created_at ? $request->created_at : now();
    $payment = Payment::create([
        'store_id'       => $request->store_id,
        'amount'         => $request->amount ?? null,
        'note'           => $request->note ?? null,
        'status'         => 'paid',
        'transaction_id' => $transactionId,
        'created_at'    => $createdAt,
        // 'updated_at'    => $createdAt,
    ]);

    // No more updating store status, reports will use payments table

    return response()->json([
        'success' => true,
        'payment' => $payment,
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
