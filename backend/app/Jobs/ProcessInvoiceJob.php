<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        try {
            // IMPORTANT: Log job version to confirm worker is using new code
            Log::info('===========================');
            Log::info('NEW JOB VERSION - STARTING');
            Log::info('===========================');

            // Step 1: Processing
            $this->invoice->update(['status' => 'processing']);
            Log::info('Invoice #' . $this->invoice->id . ' - Status: processing');

            // Get full file path
            $filePath = storage_path('app/public/' . $this->invoice->file_path);

            // Log everything about the invoice and file
            Log::info('Invoice ID: ' . $this->invoice->id);
            Log::info('Invoice data: ' . json_encode($this->invoice->toArray()));
            Log::info('File path from DB: ' . $this->invoice->file_path);
            Log::info('Full file path: ' . $filePath);
            Log::info('Real path: ' . realpath($filePath));
            Log::info('File exists: ' . (file_exists($filePath) ? 'YES' : 'NO'));

            if (file_exists($filePath)) {
                Log::info('File size: ' . filesize($filePath) . ' bytes');
            }

            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File not found: ' . $filePath);
            }

            // Step 2: Extracting (before Flask call)
            $this->invoice->update(['status' => 'extracting']);
            Log::info('Invoice #' . $this->invoice->id . ' - Status: extracting');

            // Use EXACT same code as the working /test-flask route
            Log::info("ABOUT TO SEND FILE USING FOPEN - EXACT SAME AS WORKING ROUTE");

            $file = fopen($filePath, 'r');

            $response = Http::timeout(300)
            ->connectTimeout(10)
            ->attach(
                'invoice',
                $file,
                basename($filePath)
            )
            ->post('http://127.0.0.1:5000/extract');

            // Log the response
            Log::info('Flask response status: ' . $response->status());
            Log::info('Flask response body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json('data');

                // Step 3: Completed
                $this->invoice->update([
                    'vendor'        => $data['vendor'] ?? null,
                    'invoice_date'  => $data['invoice_date'] ?? null,
                    'reference'     => $data['reference'] ?? null,
                    'tax_code'      => $data['tax_code'] ?? null,
                    'amount'        => $data['amount'] ?? null,
                    'currency'      => $data['currency'] ?? null,
                    'text'          => $data['description'] ?? null,
                    'status'        => 'completed'
                ]);

                Log::info('Invoice #' . $this->invoice->id . ' - Status: completed');
                Log::info('Invoice processed successfully: ' . $this->invoice->id);

                // Log that we're done
                Log::info('===========================');
                Log::info('JOB COMPLETED SUCCESSFULLY');
                Log::info('===========================');
            } else {
                // Log the full error response
                Log::error('Flask API error response: ' . $response->body());
                throw new \Exception(
                    'Flask API error: ' .
                    $response->status() .
                    ' - ' .
                    $response->body()
                );
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Flask connection timeout: ' . $e->getMessage());
            $this->invoice->update(['status' => 'failed']);
        } catch (\Exception $e) {
            Log::error('Processing failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $this->invoice->update(['status' => 'failed']);
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->invoice->update(['status' => 'failed']);
        Log::error('Job failed permanently: ' . $exception->getMessage());
        Log::error('Job failed stack trace: ' . $exception->getTraceAsString());
    }
}
