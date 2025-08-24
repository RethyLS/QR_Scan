<?php
use Illuminate\Http\Request; // 👈 add this
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\StoreController;
use App\Models\Store;
use App\Models\Payment;
use Carbon\Carbon;

Route::middleware([\App\Http\Middleware\CorsMiddleware::class])->group(function() {

    // Stores
    Route::get('/stores', [StoreController::class, 'index']);
    Route::post('/stores', [StoreController::class, 'store']);
    Route::put('/stores/{id}', [StoreController::class, 'update']);
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']);
    Route::get('/stores/by-date', [StoreController::class, 'byDate']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments/mark-paid', [PaymentController::class, 'markPaid']);
    // Update payment note
    Route::put('/payments/{id}/note', [PaymentController::class, 'updateNote']);


    // Reports
    Route::get('/reports', function(Request $request) {
    $selectedDate = $request->query('date') 
        ? Carbon::parse($request->query('date')) 
        : Carbon::today();

    $stores = Store::with(['payments' => function($q) use ($selectedDate) {
        $q->whereDate('created_at', $selectedDate);
    }])->get();

    $report = $stores->map(function($store) use ($selectedDate) {
        $payment = $store->payments->first();
        return [
            'date'           => $selectedDate->format('Y-m-d'),
            'store_id'       => $store->id,
            'store_name'     => $store->name,
            'owner'          => $store->owner,
            'group'          => $store->group,
            'amount'         => $payment?->amount ?? 0,
            'transaction_id' => $payment?->transaction_id ?? null,
            'note'           => $payment?->note ?? null,
            'status'         => $payment ? $payment->status : 'unpaid',
        ];
    });

    return response()->json($report);
});

});
