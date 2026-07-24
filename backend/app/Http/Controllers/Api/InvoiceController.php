<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Jobs\ProcessInvoiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class InvoiceController extends Controller
{
    // GET /api/invoices
    public function index()
    {
        return response()->json(
            Invoice::latest()->get()
        );
    }

    // POST /api/invoices
    public function store(Request $request)
    {
        $invoice = Invoice::create($request->all());
        return response()->json($invoice, 201);
    }

    // GET /api/invoices/{invoice}
    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    // PUT /api/invoices/{invoice}
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return response()->json($invoice);
    }

    // DELETE /api/invoices/{invoice}
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json([
            'message' => 'Invoice deleted successfully.'
        ]);
    }

    // PUT /api/invoices/{invoice}/status
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $invoice->status = $request->status;
        $invoice->save();

        return response()->json([
            'message' => 'Status updated.',
            'status' => $invoice->status
        ]);
    }

    // GET /api/invoices/{invoice}/status
    public function status(Invoice $invoice)
    {
        return response()->json([
            'status' => $invoice->status
        ]);
    }

    // POST /api/invoices/upload// POST /api/invoices/upload
public function upload(Request $request)
{
    try {
        $request->validate([
            'invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        // Save uploaded file
        $path = $request->file('invoice')->store('invoices', 'public');

        // Create invoice with initial status
        $invoice = Invoice::create([
            'vendor' => null,
            'invoice_date' => null,
            'reference' => null,
            'amount' => null,
            'currency' => null,
            'text' => null,
            'file_path' => $path,
            'status' => 'uploaded'
        ]);

        //  AUTO-DISPATCH the job immediately after upload
        ProcessInvoiceJob::dispatch($invoice);
        Log::info('Job dispatched for invoice: ' . $invoice->id);
        return response()->json([
            "success" => true,
            "invoice_id" => $invoice->id,
            "message" => "Invoice uploaded and processing started"
        ]);

    } catch (\Exception $e) {
        Log::error('Upload error: ' . $e->getMessage());
        return response()->json([
            "success" => false,
            "message" => $e->getMessage()
        ], 500);
    }
}
    // POST /api/invoices/{invoice}/process
    public function process(Invoice $invoice)
    {
        // Dispatch job to process invoice asynchronously
        ProcessInvoiceJob::dispatch($invoice);

        return response()->json([
            "success" => true,
            "queued" => true,
            "message" => "Processing started"
        ]);
    }

}
