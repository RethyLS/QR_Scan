<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::get('/pay', function(Request $request) {
    // Pass store_id, amount, and optional date to controller
    $postRequest = new Request([
        'store_id'   => $request->query('store_id'),
        'amount'     => $request->query('amount'),
        'created_at' => $request->query('date'), // new: use selected date from QR
    ]);

    $controller = new PaymentController();
    $response = $controller->markPaid($postRequest);

    $data = $response->getData();

    if (!isset($data->success) || !$data->success) {
        return response("<h1 style='color:red;'>❌ Payment Failed</h1>", 400)
            ->header('Content-Type', 'text/html');
    }

    $payment = $data->payment;
    $store   = \App\Models\Store::find($payment->store_id);

    $html = "
        <html>
            <head><title>Payment Success</title></head>
            <body style='font-family: Arial; text-align:center; margin-top:50px;'>

                <h1 style='color:green;'>✅ Payment Successful</h1>
                <p><strong>Store:</strong> {$store->name} (ID: {$payment->store_id})</p>
                <p><strong>Amount:</strong> {$payment->amount}</p>
                <p><strong>Status:</strong> {$payment->status}</p>
                <p><strong>Date:</strong> {$payment->created_at}</p>
                
                <script>
                // Wait 2 seconds then refresh the page
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                </script>

            </body>
        </html>
    ";

    return response($html)->header('Content-Type', 'text/html');
});



