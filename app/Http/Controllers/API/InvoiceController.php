<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all invoices with full details
     */
    public function index()
    {
        $invoices = Invoice::with([
            'customer',
            'exchangeRate',
            'discount',
            'deliveryFee',
            'image',
            'invoiceItems'
        ])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Store a new invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_date'      => 'required|date',
            'customer_id'       => 'required|exists:customers,id',
            'exchange_rate_id'  => 'nullable|exists:exchange_rates,id',
            'discount_id'       => 'nullable|exists:discounts,id',
            'delivery_fee_id'   => 'nullable|exists:delivery_fees,id',
            'image_id'          => 'nullable|exists:files,id',
            'vat_percent'       => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
        ]);

        $invoice = Invoice::create($request->only([
            'invoice_date',
            'customer_id',
            'exchange_rate_id',
            'image_id',
            'discount_id',
            'delivery_fee_id',
            'vat_percent',
            'note',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Invoice created successfully',
            'data' => $invoice
        ], 201);
    }

    /**
     * Show invoice with detailed calculation
     */


/**
 * Show invoice with detailed calculation and Share Link
 */
public function show($id)
{
    // 1. Fetch Invoice with all necessary relationships
    $invoice = Invoice::with([
        'customer',
        'exchangeRate',
        'discount',
        'deliveryFee',
        'image',
        'invoiceItems.item' // Ensure item relationship is loaded
    ])->findOrFail($id);

    // 2. Subtotal Calculation
    // Use 'unit_price' based on your InvoiceItemController logic
    $subtotal = $invoice->invoiceItems->sum(fn($item) => $item->quantity * $item->unit_price);

    // 3. Discount Calculation
    $discountAmount = 0;
    if ($invoice->discount) {
        if ($invoice->discount->percent) {
            $discountAmount = ($subtotal * $invoice->discount->percent) / 100;
        } elseif ($invoice->discount->amount) {
            $discountAmount = $invoice->discount->amount;
        }
    }

    // 4. VAT Calculation
    $vatAmount = 0;
    if ($invoice->vat_percent) {
        $vatAmount = (($subtotal - $discountAmount) * $invoice->vat_percent) / 100;
    }

    // 5. Delivery Fee
    $deliveryFee = $invoice->deliveryFee?->amount ?? 0;

    // 6. Total Calculation
    $total = $subtotal - $discountAmount + $vatAmount + $deliveryFee;

    // 7. Converted total (Exchange Rate)
    $convertedTotal = $invoice->exchangeRate ? $total * $invoice->exchangeRate->rate : null;

    // 8. GENERATE SIGNED SHARE URL (Valid for 7 days)
    // This allows the mobile app to share a link that opens the PDF without login
    $shareUrl = URL::temporarySignedRoute(
        'api.invoice.pdf', 
        now()->addDays(7), 
        ['id' => $id]
    );

    return response()->json([
        'status' => true,
        'share_url' => $shareUrl, // Give this to mobile developer
        'invoice' => $invoice,
        'calculation' => [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discountAmount, 2),
            'vat' => round($vatAmount, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'total' => round($total, 2),
            'converted_total' => $convertedTotal ? round($convertedTotal, 2) : null,
            'currency' => $invoice->exchangeRate?->currency,
        ]
    ]);
}

    /**
     * Update invoice
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'invoice_date'      => 'nullable|date',
            'customer_id'       => 'nullable|exists:customers,id',
            'exchange_rate_id'  => 'nullable|exists:exchange_rates,id',
            'discount_id'       => 'nullable|exists:discounts,id',
            'delivery_fee_id'   => 'nullable|exists:delivery_fees,id',
            'image_id'          => 'nullable|exists:files,id',
            'vat_percent'       => 'nullable|numeric|min:0',
            'note'              => 'nullable|string',
        ]);

        $invoice->update($request->only([
            'invoice_date',
            'customer_id',
            'exchange_rate_id',
            'image_id',
            'discount_id',
            'delivery_fee_id',
            'vat_percent',
            'note',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Invoice updated successfully',
            'data' => $invoice
        ]);
    }

    /**
     * Delete invoice
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json([
            'status' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }

    public function downloadPdf(Request $request, $id)
{
    // Validate signature (security for public links)
    if (! $request->hasValidSignature()) {
        abort(401, 'This link has expired or is invalid.');
    }

    $invoice = Invoice::with(['customer', 'invoiceItems.item', 'discount', 'deliveryFee'])->findOrFail($id);

    // Re-use your calculation logic
    $subtotal = $invoice->invoiceItems->sum(fn($item) => $item->quantity * $item->unit_price);
    $discountAmount = 0;
    if ($invoice->discount) {
        $discountAmount = $invoice->discount->percent ? ($subtotal * $invoice->discount->percent / 100) : $invoice->discount->amount;
    }
    $total = $subtotal - $discountAmount + ($invoice->deliveryFee?->amount ?? 0);

    $calc = [
        'subtotal' => $subtotal,
        'discount' => $discountAmount,
        'total'    => $total
    ];

    $mpdf = app('mPdf');
    $mpdf->WriteHTML(view('pdf.invoice', compact('invoice', 'calc'))->render());
    return response($mpdf->Output('', 'S'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="Invoice_' . $invoice->id . '.pdf"',
    ]);
}
  

}
