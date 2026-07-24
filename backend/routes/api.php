<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Http;


Route::get('/invoices', [InvoiceController::class, 'index']);
Route::apiResource('invoices', InvoiceController::class);

Route::post('/invoices/upload', [InvoiceController::class, 'upload']);

Route::get('/invoices/{invoice}/status', [InvoiceController::class, 'status']);

Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus']);

Route::post('/invoices/{invoice}/process', [InvoiceController::class, 'process']);
Route::get('/test-flask', function () {

    $file = storage_path('app/public/invoices/facture1.png');

    return Http::attach(
        'invoice',
        fopen($file, 'r'),
        basename($file)
    )->post('http://127.0.0.1:5000/extract')->json();
});
Route::get('/files/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'File not found: ' . $fullPath], 404);
    }

    // Get the file extension
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

    // Set appropriate headers based on file type
    $headers = [];
    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $headers['Content-Type'] = mime_content_type($fullPath);
    } elseif (strtolower($extension) === 'pdf') {
        $headers['Content-Type'] = 'application/pdf';
    }

    return response()->file($fullPath, $headers);
})->where('path', '.*');
