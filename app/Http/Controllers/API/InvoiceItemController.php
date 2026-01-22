<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceItem;
use App\Models\Invoice;

class InvoiceItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /invoice-items
     * Full detail query
     */
    public function index(Request $request)
    {
$invoiceItems = InvoiceItem::query()
    ->with([
        'item:id,item_name',
        'invoice:id,invoice_date,customer_id,discount_id,delivery_fee_id,exchange_rate_id,vat_percent',
        'invoice.customer:id,name,phone',
        'invoice.discount:id,percent,amount',
        'invoice.deliveryFee:id,amount',
        'invoice.exchangeRate:id,currency,rate',
    ])
    ->select('id','invoice_id','item_id','unit_price','quantity','status')
    ->get()
    ->map(function ($item) {

        // 1️⃣ Subtotal
        $subtotal = $item->unit_price * $item->quantity;

        // 2️⃣ Discount
        $discountAmount = 0;
        if ($item->invoice->discount) {
            if (!is_null($item->invoice->discount->percent)) {
                $discountAmount = ($subtotal * $item->invoice->discount->percent) / 100;
            } else {
                $discountAmount = $item->invoice->discount->amount;
            }
        }

        // 3️⃣ VAT
        $vatAmount = 0;
        if (!is_null($item->invoice->vat_percent)) {
            $vatAmount = (($subtotal - $discountAmount) * $item->invoice->vat_percent) / 100;
        }

        // 4️⃣ Delivery Fee
        $deliveryFee = $item->invoice->deliveryFee?->amount ?? 0;

        // 5️⃣ Total
        $total = $subtotal - $discountAmount + $vatAmount + $deliveryFee;

        // 6️⃣ Exchange Rate
        $convertedTotal = $item->invoice->exchangeRate
            ? $total * $item->invoice->exchangeRate->rate
            : null;

        return [
            'invoice_item_id' => $item->id,
            'status' => $item->status,
            'invoice' => [
                'id' => $item->invoice->id,
                'invoice_date' => $item->invoice->invoice_date,
                'customer_id' => $item->invoice->customer_id,
                'exchange_rate_id' => $item->invoice->exchange_rate_id,
                'discount_id' => $item->invoice->discount_id,
                'delivery_fee_id' => $item->invoice->delivery_fee_id,
                'image_id' => $item->invoice->id,
                'vat_percent' => $item->invoice->vat_percent,
                'note' => $item->invoice->note,
            ], 


            'invoice_date' => $item->invoice->invoice_date,

            'customer' => [
                'name' => $item->invoice->customer->name,
                'phone' => $item->invoice->customer->phone,
            ],

            'item' => [
                'id' => $item->item_id,
                'name' => $item->item->item_name,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
            ],

            'calculation' => [
                'subtotal' => round($subtotal, 2),
                'discount' => round($discountAmount, 2),
                'vat' => round($vatAmount, 2),
                'delivery_fee' => round($deliveryFee, 2),
                'total' => round($total, 2),
                'converted_total' => $convertedTotal ? round($convertedTotal, 2) : null,
                'currency' => $item->invoice->exchangeRate?->currency,
            ]
        ];
    });


        return response()->json([
            'status' => true,
            'data' => $invoiceItems
        ]);
    }

    /**
     * GET /invoice-items/{id}
     */
    public function show($id)
    {
        $item = InvoiceItem::with([
            'item',
            'invoice.customer',
            'invoice.discount',
            'invoice.deliveryFee',
            'invoice.exchangeRate'
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }

    /**
     * POST /invoice-items
     */


    /**
     * PUT /invoice-items/{id}
     */
    public function update(Request $request, $id)
    {
        $item = InvoiceItem::findOrFail($id);

        $request->validate([
            'item_id' => 'sometimes|exists:items,id',
            'unit_price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:paid,unpaid',
        ]);

        $item->update($request->only([
            'item_id',
            'unit_price',
            'quantity',
            'status'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Invoice item updated',
            'data' => $item
        ]);
    }

    /**
     * DELETE /invoice-items/{id}
     */
    public function destroy($id)
    {
        InvoiceItem::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Invoice item deleted'
        ]);
    }
    /**
 * GET /api/invoice/template/{invoiceId}
 * HTML template for mobile (WebView)
 */
// public function template($invoiceId, Request $request)
// {
//     $lang = $request->query('lang', 'en'); // en | km

//     $invoice = Invoice::with([
//         'customer:id,name,phone',
//         'discount:id,percent,amount',
//         'deliveryFee:id,amount',
//         'exchangeRate:id,currency,rate',
//         'invoiceItems.item:id,item_name'
//     ])->findOrFail($invoiceId);

//     return view('invoices.template', [
//         'invoice' => $invoice,
//         'lang' => $lang
//     ]);
// }

/**
 * GET /api/invoice/pdf/{invoiceId}
 * Generate PDF (Khmer + English)
 */
// public function pdf($invoiceId, Request $request)
// {
//     $lang = $request->query('lang', 'en'); // en | km

//     $invoice = Invoice::with([
//         'customer:id,name,phone',
//         'discount:id,percent,amount',
//         'deliveryFee:id,amount',
//         'exchangeRate:id,currency,rate',
//         'invoiceItems.item:id,item_name'
//     ])->findOrFail($invoiceId);

//     $pdf = Pdf::loadView(
//         'invoices.template',
//         compact('invoice', 'lang')
//     )->setPaper('A4', 'portrait');

//     return $pdf->stream("invoice-{$invoice->id}.pdf");
// }

/**
 * POST /invoice-items
 * Store invoice items with multiple item_id
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'item_id' => 'required|array|min:1',
        'item_id.*' => 'exists:items,id',
        'unit_price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
    ]);

    $invoice = Invoice::findOrFail($validated['invoice_id']);

    $createdItems = [];

    foreach ($validated['item_id'] as $itemId) {
        $createdItems[] = $invoice->invoiceItems()->create([
            'item_id' => $itemId,
            'unit_price' => $validated['unit_price'],
            'quantity' => $validated['quantity'],
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Invoice items created successfully',
        'data' => $createdItems
    ], 201);
}


public function downloadPDF($id)
{
    // 1. Fetch the full invoice with all items
    $invoice = Invoice::with(['customer', 'invoiceItems.item', 'discount', 'exchangeRate'])
                      ->findOrFail($id);

    // 2. Perform calculations (similar to your index logic)
    $subtotal = $invoice->invoiceItems->sum(function($item) {
        return $item->unit_price * $item->quantity;
    });
    
    $discount = 0;
    if ($invoice->discount) {
        $discount = $invoice->discount->percent 
            ? ($subtotal * $invoice->discount->percent / 100) 
            : $invoice->discount->amount;
    }

    $calc = [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total' => $subtotal - $discount
    ];

    // 3. Generate PDF
    $mpdf = app('mPdf');
    $mpdf->WriteHTML(view('pdf.invoice', compact('invoice', 'calc'))->render());

    return response($mpdf->Output('', 'S'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="invoice-' . $invoice->id . '.pdf"',
    ]);

    
}


    
}
