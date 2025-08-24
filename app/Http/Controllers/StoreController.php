<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Payment;
use Carbon\Carbon;

class StoreController extends Controller
{
    // List stores with latest payment
    public function index()
    {
        $stores = Store::with(['payments' => function ($q) {
            $q->latest()->limit(1); // only latest payment
        }])->get()->map(function ($store) {
            return [
                'id'             => $store->id,
                'stall_id'       => $store->stall_id,
                'name'           => $store->name,
                'owner'          => $store->owner,
                'group'          => $store->group,
                'default_amount' => $store->default_amount,
                'status'         => $store->status,
                'latest_payment' => $store->payments->first() ?? null, // safe even if no payment
            ];
        });

        return response()->json(['stores' => $stores]);
    }

    // Create store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stall_id'       => 'required|unique:stores',
            'name'           => 'required',
            'owner'          => 'required',
            'group'          => 'nullable',
            'default_amount' => 'required|numeric',
        ]);

        $store = Store::create($validated);

        return response()->json($store, 201);
    }

    // Update store
    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        $validated = $request->validate([
            'stall_id'       => 'required|unique:stores,stall_id,' . $id,
            'name'           => 'required',
            'owner'          => 'required',
            'group'          => 'nullable',
            'default_amount' => 'required|numeric',
        ]);

        $store->update($validated);

        return response()->json($store);
    }

    // Delete store
    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    // Mark store as paid (for collector)
    public function markPaid(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'amount'   => 'required|numeric',
            'note'     => 'nullable|string',
        ]);

        $payment = Payment::create([
            'store_id' => $request->store_id,
            'amount'   => $request->amount,
            'note'     => $request->note ?? null,
            'status'   => 'paid',
        ]);

        $store = Store::find($request->store_id);
        $store->update(['status' => 'paid']);

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'store'   => $store,
        ]);
    }

    // For note
    public function byDate(Request $request)
{
    $date = $request->query('date'); // expected format: YYYY-MM-DD
    $day  = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();

    // Load only payments that happened on that date
    $stores = Store::with(['payments' => function ($q) use ($day) {
        $q->whereDate('created_at', $day)->orderByDesc('created_at');
    }])->get();

    // Return the SAME JSON shape your Flutter expects (has latest_payment)
    $payload = $stores->map(function ($s) {
        $p = $s->payments->first(); // payment for that date (or null)

        return [
            'id'             => $s->id,
            'stall_id'       => $s->stall_id,
            'name'           => $s->name,
            'owner'          => $s->owner,
            'group'          => $s->group,
            'default_amount' => (float) $s->default_amount,
            'status'         => $s->status,
            'latest_payment' => $p ? [
                'id'             => $p->id,
                'amount'         => (float) $p->amount,
                'note'           => $p->note,
                'created_at'     => optional($p->created_at)->toISOString(),
                'transaction_id' => $p->transaction_id,
            ] : null,
        ];
    });

    return response()->json(['stores' => $payload]);
}

}

