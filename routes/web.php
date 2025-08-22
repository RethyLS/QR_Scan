<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('/pay-test', function() {
    return "Payment received successfully!";
});

Route::get('/pay', function(Request $request) {
    $postRequest = new Request([
        'store_id' => $request->query('store_id'),
        'amount'   => $request->query('amount'),
    ]);

    $controller = new PaymentController();
    $response = $controller->markPaid($postRequest);

    $data = $response->getData();

    if (!isset($data->success) || !$data->success) {
        return response("<h1 style='color:red;'>❌ Payment Failed</h1>", 400)
            ->header('Content-Type', 'text/html');
    }

    $payment = $data->payment;
    $store   = $data->store;

    $html = "
        <html>
            <head><title>Payment Success</title></head>
            <body style='font-family: Arial; text-align:center; margin-top:50px;'>
                <h1 style='color:green;'>✅ Payment Successful</h1>
                <p><strong>Store:</strong> {$store->name} (ID: {$payment->store_id})</p>
                <p><strong>Amount:</strong> {$payment->amount}</p>
                <p><strong>Status:</strong> {$payment->status}</p>
            </body>
        </html>
    ";

    return response($html)->header('Content-Type', 'text/html');
});


