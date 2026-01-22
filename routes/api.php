<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\VatCustomerController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\DeliveryFeeController;
use App\Http\Controllers\API\ExchangeRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\InvoiceController;

use App\Http\Controllers\API\BusinessInfoController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\SignatureController;
use App\Http\Controllers\API\InvoiceItemController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Register phone (send OTP)
Route::post('/register', [UserController::class, 'register']);

// Verify OTP and get token
Route::post('/verify-otp', [UserController::class, 'verifyOtp']);

// Optional login with phone only (if you don't want OTP)
Route::post('/login', [UserController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Get current user info
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    });

    // Logout
    Route::post('/logout', [UserController::class, 'logout']);

    // Optional: user CRUD
    Route::apiResource('users', UserController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/files', [FileController::class, 'index']);
    // Route::post('/files', [FileController::class, 'store']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
     Route::post('/files', [FileController::class, 'upload']);
     Route::put('/files/{id}', [FileController::class, 'update']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vat-customers', [VatCustomerController::class, 'index']);
    Route::post('/vat-customers', [VatCustomerController::class, 'store']);
    Route::get('/vat-customers/{id}', [VatCustomerController::class, 'show']);
    Route::put('/vat-customers/{id}', [VatCustomerController::class, 'update']);
    Route::delete('/vat-customers/{id}', [VatCustomerController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::get('/discounts/{id}', [DiscountController::class, 'show']);
    Route::put('/discounts/{id}', [DiscountController::class, 'update']);
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/delivery-fees', [DeliveryFeeController::class, 'index']);
    Route::post('/delivery-fees', [DeliveryFeeController::class, 'store']);
    Route::get('/delivery-fees/{id}', [DeliveryFeeController::class, 'show']);
    Route::put('/delivery-fees/{id}', [DeliveryFeeController::class, 'update']);
    Route::delete('/delivery-fees/{id}', [DeliveryFeeController::class, 'destroy']);
});

Route::post(
    '/exchange-rates/{id}/toggle',
    [ExchangeRateController::class, 'toggle']
)->middleware('auth:sanctum');
Route::apiResource(
    '/exchange-rates',
    ExchangeRateController::class
)->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/invoices', [InvoiceController::class, 'index']);

    Route::post('/invoices', [InvoiceController::class, 'store']);

    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);

    Route::put('/invoices/{id}', [InvoiceController::class, 'update']);

    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);

});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/invoice-items', [InvoiceItemController::class, 'index']);

    Route::get('/invoice-items/{id}', [InvoiceItemController::class, 'show']);

    Route::post('/invoice-items', [InvoiceItemController::class, 'store']);

    Route::put('/invoice-items/{id}', [InvoiceItemController::class, 'update']);

    Route::delete('/invoice-items/{id}', [InvoiceItemController::class, 'destroy']);

});




Route::middleware('auth:sanctum')->group(function () {

    Route::get('/business-infos', [BusinessInfoController::class, 'index']);

    Route::post('/business-infos', [BusinessInfoController::class, 'store']);

    Route::get('/business-infos/{id}', [BusinessInfoController::class, 'show']);

    Route::put('/business-infos/{id}', [BusinessInfoController::class, 'update']);

    Route::delete('/business-infos/{id}', [BusinessInfoController::class, 'destroy']);

});


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/payments', [PaymentController::class, 'index']);

    Route::post('/payments', [PaymentController::class, 'store']);

    Route::get('/payments/{id}', [PaymentController::class, 'show']);

    Route::put('/payments/{id}', [PaymentController::class, 'update']);

    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

});



Route::middleware('auth:sanctum')->group(function () {

    Route::get('/signatures', [SignatureController::class, 'index']);

    Route::post('/signatures', [SignatureController::class, 'store']);

    Route::get('/signatures/{id}', [SignatureController::class, 'show']);

    Route::put('/signatures/{id}', [SignatureController::class, 'update']);

    Route::delete('/signatures/{id}', [SignatureController::class, 'destroy']);

});

Route::middleware('auth:sanctum')->group(function () {

    // HTML template (mobile WebView)
    Route::get(
        '/invoice/template/{invoiceId}',
        [InvoiceItemController::class, 'template']
    );

    // PDF
    Route::get(
        '/invoice/pdf/{invoiceId}',
        [InvoiceItemController::class, 'pdf']
    );
});
Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'downloadPdf'])
    ->middleware('auth:sanctum');
// If you want it public for sharing:
Route::get('share/invoice/{id}', [InvoiceItemController::class, 'downloadPDF'])->name('invoice.share');

// Public share route (Protected by Signature)
Route::get('invoices/{id}/pdf', [App\Http\Controllers\API\InvoiceController::class, 'downloadPdf'])
    ->name('api.invoice.pdf');

    